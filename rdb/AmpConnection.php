<?php

namespace r;

use Amp\DeferredFuture;
use Amp\Future;
use Amp\Socket\ConnectContext;
use Amp\Socket\Socket;
use Amp\Sync\LocalMutex;
use Amp\Sync\Mutex;
use r\Datum\ArrayDatum;
use r\Datum\BoolDatum;
use r\Datum\NullDatum;
use r\Datum\NumberDatum;
use r\Datum\ObjectDatum;
use r\Datum\StringDatum;
use r\Exceptions\RqlDriverError;
use r\Exceptions\RqlServerError;
use r\Options\FormatMode;
use r\Options\RunOptions;
use r\ProtocolBuffer\QueryQueryType;
use r\ProtocolBuffer\ResponseResponseType;
use r\Queries\Dbs\Db;

use function Amp\async;

class AmpConnection extends Connection
{
	public string $defaultDbName;
	private Socket|null $socket = null;
	private string $host;
	private int $port;
	private Db|null $defaultDb;
	private string $user;
	private string $password;
	private array $activeTokens = [];
	private int|null $timeout;
	private array|bool|null $ssl;

	private \SplQueue $buffer;

	public function __construct(ConnectionOptions $connectionOptions)
	{
		$this->host = $connectionOptions->host;
		$this->port = $connectionOptions->port;
		$this->user = $connectionOptions->user;
		$this->password = $connectionOptions->password;
		$this->timeout = $connectionOptions->timeout;
		$this->ssl = $connectionOptions->ssl;

		$this->useDb($connectionOptions->db);
		$this->setTimeout($connectionOptions->timeout);

		$this->buffer = new \SplQueue();

		$this->connect()->await();
	}

	public function useDb(string|null $dbName): void
	{
		if (empty($dbName)) {
			$this->defaultDb = null;
			$this->defaultDbName = '';
			return;
		}

		$this->defaultDbName = $dbName;
		$this->defaultDb = new Db($dbName);
	}

	public function setTimeout(int|float|null $timeout): void
	{
		if (empty($timeout)) {
			return;
		}

		$this->timeout = $timeout;
	}

	private function connect(): Future
	{
		return async(function () {
			if ($this->isOpen()) {
				throw new RqlDriverError("Already connected");
			}

			$context = (new ConnectContext())->withTcpNoDelay();
			if ($this->timeout !== null) {
				$context = $context->withConnectTimeout($this->timeout);
			}
			$this->socket = \Amp\Socket\connect('tcp://' . $this->host . ':' . $this->port, $context);

			if ($this->ssl) {
				$this->socket->setupTls();
			}

			$handshake = new Handshake($this->user, $this->password);
			$handshakeResponse = null;

            while(true) {
                try {
                    $msg = $handshake->nextMessage($handshakeResponse);
                } catch (\Throwable $e) {
                    $this->close(false);
                    throw $e;
                }
                if ($msg === null) {
                    $this->onReceive();
                    return;
                }
                if ($msg !== '') {
                    $this->sendStr($msg);
                }
                $handshakeResponse = $this->readStr();
            }
		});
	}

	public function isOpen(): bool
	{
		return $this->socket?->isClosed() === false;
	}

	public function close(bool $noreplyWait = true): void
	{
		if (!$this->isOpen()) {
			throw new RqlDriverError("Not connected.");
		}

		if ($noreplyWait) {
			$this->noreplyWait();
		}

		$this->socket->close();
		$this->socket = null;
		$this->activeTokens = [];
	}

	public function noreplyWait(): void
	{
		if (!$this->isOpen()) {
			throw new RqlDriverError("Not connected.");
		}

		// Generate a token for the request
		$token = $this->generateToken();

		// Send the request
		$jsonQuery = [QueryQueryType::PB_NOREPLY_WAIT->value];
		$this->sendQuery($token, $jsonQuery);

		// Await the response
		$response = $this->receiveResponse($token)->await();
		$type = ResponseResponseType::tryFrom($response['t']);

		if ($type !== ResponseResponseType::PB_WAIT_COMPLETE) {
			throw new RqlDriverError("Unexpected response type to noreplyWait query.");
		}
	}

	private function generateToken(): int
	{
		static $token = -1;
		$token = ($token + 1) % (1 << 30);
		return $token;
	}

	private function sendQuery(int $token, mixed $json): void
	{
		// PHP by default loses some precision when encoding floats, so we temporarily
		// bump up the `precision` option to avoid this.
		// The 17 assumes IEEE-754 double precision numbers.
		// Source: http://docs.oracle.com/cd/E19957-01/806-3568/ncg_goldberg.html
		//         "The same argument applied to double precision shows that 17 decimal
		//          digits are required to recover a double precision number."
		$previousPrecision = ini_set("precision", 17);
		$request = json_encode($json);
		if ($previousPrecision !== false) {
			ini_set("precision", $previousPrecision);
		}
		if ($request === false) {
			throw new RqlDriverError("Failed to encode query as JSON: " . json_last_error());
		}

		$requestSize = pack("V", strlen($request));
		$binaryToken = pack("V", $token) . pack("V", 0);
		$this->sendStr($binaryToken . $requestSize . $request);
	}

	private function sendStr(string $s): void
	{
		$this->socket->write($s);
	}

	/**
	 * @param int $token
	 * @param Query|null $query
	 * @param bool $noChecks
	 * @return Future<array>
	 */
	private function receiveResponse(int $token, Query $query = null, bool $noChecks = false): Future
	{
		$future = new DeferredFuture();
		$this->activeTokens[$token] = [$noChecks, $future->complete(...), $query, $future->error(...)];

		return $future->getFuture();
	}

	private function onReceive(): Future
	{
		return async(function () {
            while(true) {
                $mutex = new LocalMutex();
                $lock = $mutex->acquire();
                $response = '';
                $remaining = 4 + 8;
                header_read:
                $partialResponse = $this->socket->read(limit: $remaining);
                if ($partialResponse === null || $partialResponse === false) {
                    throw new RqlDriverError('RethinkDB: Broken Pipe.');
                }
                if (strlen($response) <= $remaining) {
                    $response .= $partialResponse;
                    $remaining -= strlen($partialResponse);
                    if (strlen($response) < 4 + 8) {
                        goto header_read;
                    }
                }
                $header = unpack('Vtoken/Vtoken2/Vsize', $response ?? '');
                $token = $header['token'];
                if ($header['token2'] !== 0) {
                    throw new RqlDriverError('Invalid response from server: Invalid token.');
                }
                $size = $header['size'];
                $partialResponse = '';
                continue_it:
                $response = $this->socket->read(limit: $size);

                if (strlen($response) !== $size) {
                    $partialResponse .= $response;
                    $size -= strlen($response);
                    goto continue_it;
                }

                if (!empty($partialResponse)) {
                    $response = $partialResponse . $response;
                }

                try {
                    $response = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
                } catch (\JsonException $exception) {
                    throw new RqlDriverError('Invalid response from server: Failed to decode JSON.', previous: $exception);
                }

                $handler = $this->activeTokens[$token] ?? null;
                if ($handler === null) {
                    throw new RqlDriverError('Unexpected response received from server: ' . json_encode($response));
                }

                [$check, $respond, $query, $error] = $handler;
                $errored = false;
                try {
                    if (!$check) {
                        $this->checkResponse($response, $query);
                    }
                } catch (\Throwable $e) {
                    $error($e);
                    $errored = true;
                } finally {
                    unset($this->activeTokens[$token]);
                }

                if (!$errored) {
                    $respond($response);
                }

                $lock->release();
            }
		})->ignore();
	}

	private function checkResponse(array $response, Query $query = null): void
	{
		$type = ResponseResponseType::tryFrom(
			$response['t'] ?? throw new RqlDriverError("Response message has no type.")
		);

		if ($type === ResponseResponseType::PB_CLIENT_ERROR) {
			throw new RqlDriverError("Server says PHP-RQL is buggy: " . $response['r'][0]);
		}

		if ($type === ResponseResponseType::PB_COMPILE_ERROR) {
			$backtrace = null;
			if (isset($response['b'])) {
				$backtrace = Backtrace::decodeServerResponse($response['b']);
			}
			throw new RqlServerError("Compile error: " . $response['r'][0], $query, $backtrace);
		}

		if ($type === ResponseResponseType::PB_RUNTIME_ERROR) {
			$backtrace = null;
			if (isset($response['b'])) {
				$backtrace = Backtrace::decodeServerResponse($response['b']);
			}
			throw new RqlServerError("Runtime error: " . $response['r'][0], $query, $backtrace);
		}
	}

	private function readStr(): string
	{
        continue_it:
        if($this->buffer->isEmpty()) {
            $response = $this->socket->read();
            foreach(array_filter(explode("\0", $response)) as $value) {
                $this->buffer->enqueue($value);
            }
        }
        if($this->buffer->isEmpty()) {
            goto continue_it;
        }
        return $this->buffer->dequeue();
	}

	public function __destruct()
	{
		if ($this->isOpen()) {
			$this->close(false);
		}
	}

	public function reconnect(bool $noreplyWait = true): void
	{
		if ($this->isOpen()) {
			$this->close($noreplyWait);
		}
		$this->connect()->await();
	}

	/**
	 * @return array<string>|string
	 * @throws RqlDriverError
	 */
	public function server(): array|string
	{
		if (!$this->isOpen()) {
			throw new RqlDriverError("Not connected.");
		}

		// Generate a token for the request
		$token = $this->generateToken();

		// Send the request
		$jsonQuery = [QueryQueryType::PB_SERVER_INFO->value];
		$this->sendQuery($token, $jsonQuery);

		// Await the response
		$response = $this->receiveResponse($token)->await();
		$type = ResponseResponseType::tryFrom($response['t']);

		if ($type !== ResponseResponseType::PB_SERVER_INFO) {
			throw new RqlDriverError("Unexpected response type to server info query.");
		}

		$toNativeOptions = array();
		return $this->createDatumFromResponse($response)->toNative($toNativeOptions);
	}

	private function createDatumFromResponse(
		$response
	): ObjectDatum|StringDatum|BoolDatum|NumberDatum|NullDatum|ArrayDatum {
		return self::decodedJSONToDatum($response['r'][0]);
	}

	public function run(
		Query $query,
		RunOptions $options = new RunOptions(),
		string|null &$profile = ''
	): Cursor|array|string|null|\DateTimeInterface|float|int|bool {
		if (!$this->isOpen()) {
			throw new RqlDriverError("Not connected.");
		}

		// Grab PHP-RQL specific options
		$toNativeOptions = [
			'binaryFormat' => $options->binary_format ?? FormatMode::Native,
			'timeFormat' => $options->time_format ?? FormatMode::Native,
		];

		// Generate a token for the request
		$token = $this->generateToken();

		// Send the request
		$globalOptargs = $this->convertOptions($options);
		$globalOptargs['db'] = $globalOptargs['db'] ?? $this->defaultDb?->encodeServerRequest();

		$jsonQuery = [
			QueryQueryType::PB_START->value,
			$query->encodeServerRequest(),
			(object)array_filter($globalOptargs),
		];

		$this->sendQuery($token, $jsonQuery);

		if ($options->noreply) {
			return null;
		}

		// Await the response
		$response = $this->receiveResponse($token, $query)->await();
		$type = ResponseResponseType::tryFrom($response['t']);

		if ($type === ResponseResponseType::PB_SUCCESS_PARTIAL) {
			$this->activeTokens[$token] = true;
		}

		if (isset($response['p'])) {
			$profile = self::decodedJSONToDatum($response['p'])->toNative($toNativeOptions);
		}

		if ($type === ResponseResponseType::PB_SUCCESS_ATOM) {
			return $this->createDatumFromResponse($response)->toNative($toNativeOptions);
		}

		return $this->createCursorFromResponse($response, $token, $response['n'] ?? [], $toNativeOptions);
	}

	private function convertOptions(array|object|null $options): array
	{
		$opts = [];

		foreach ((array)$options as $key => $value) {
			if (in_array($key, ['binary_format', 'time_format']) || null === $value) {
				continue;
			}
			$opts[$key] = $this->nativeToDatum($value)->encodeServerRequest();
		}
		return $opts;
	}

	private function createCursorFromResponse($response, $token, $notes, $toNativeOptions): Cursor
	{
		return new Cursor($this, $response, $token, $notes, $toNativeOptions);
	}

	public function continueQuery(int $token): array
	{
		if (!$this->isOpen()) {
			throw new RqlDriverError("Not connected.");
		}

		// Send the request
		$jsonQuery = [QueryQueryType::PB_CONTINUE->value];
		$this->sendQuery($token, $jsonQuery);

		// Await the response
		$response = $this->receiveResponse($token)->await();
		$type = ResponseResponseType::tryFrom($response['t']);

		if ($type != ResponseResponseType::PB_SUCCESS_PARTIAL) {
			unset($this->activeTokens[$token]);
		}

		return $response;
	}

	public function stopQuery(int $token): array
	{
		if (!$this->isOpen()) {
			throw new RqlDriverError("Not connected.");
		}

		// Send the request
		$jsonQuery = [QueryQueryType::PB_STOP->value];
		$this->sendQuery($token, $jsonQuery);

		// Await the response (but don't check for errors. the stop response doesn't even have a type)
		$response = $this->receiveResponse($token, null, true)->await();

		unset($this->activeTokens[$token]);

		return $response;
	}
}
