<?php

namespace r;

use Iterator;
use r\Exceptions\RqlDriverError;
use r\ProtocolBuffer\ResponseResponseType;

class Cursor implements Iterator
{

    private int $token;
    private Connection $connection;
    private array $notes;
    private array $toNativeOptions;
    private array $currentData;
    private int $currentSize;
    private int $currentIndex;
    private bool $isComplete;
    private bool $wasIterated;

    public function __construct(
        Connection $connection,
        array $initialResponse,
        int $token,
        array $notes,
        array $toNativeOptions
    ) {
        $this->connection = $connection;
        $this->token = $token;
        $this->notes = $notes;
        $this->toNativeOptions = $toNativeOptions;
        $this->wasIterated = false;

        $this->setBatch($initialResponse);
    }

    // PHP iterator interface

    private function setBatch(array $response): void
    {
        $dc = new DatumConverter;
        $type = ResponseResponseType::tryFrom($response['t']);
        $this->isComplete = $type === ResponseResponseType::PB_SUCCESS_SEQUENCE;
        $this->currentIndex = 0;
        $this->currentSize = \count($response['r']);
        $this->currentData = [];
        foreach ($response['r'] as $row) {
            $this->currentData[] = $dc->decodedJSONToDatum($row);
        }
    }

    public function rewind(): void
    {
        if ($this->wasIterated) {
            throw new RqlDriverError("Rewind() not supported. You can only iterate over a cursor once.");
        }
    }

    public function next(): void
    {
        if (!$this->valid()) {
            throw new RqlDriverError("No more data available.");
        }
        $this->wasIterated = true;
        $this->currentIndex++;
    }

    public function valid(): bool
    {
        $this->requestMoreIfNecessary();
        return !$this->isComplete || ($this->currentIndex < $this->currentSize);
    }

    private function requestMoreIfNecessary(): void
    {
        while ($this->currentIndex == $this->currentSize) {
            // We are at the end of currentData. Request more if available
            if ($this->isComplete) {
                return;
            }
            $this->requestNewBatch();
        }
    }

    private function requestNewBatch(): void
    {
        try {
            $response = $this->connection->continueQuery($this->token);
            $this->setBatch($response);
        } catch (\Exception $e) {
            $this->isComplete = true;
            $this->close();
            throw $e;
        }
    }

    public function close(): void
    {
        if (!$this->isComplete) {
            // Cancel the request
            $this->connection->stopQuery($this->token);
            $this->isComplete = true;
        }
        $this->currentIndex = 0;
        $this->currentSize = 0;
        $this->currentData = array();
    }

    public function key(): mixed
    {
        return $this->currentIndex;
    }

    public function current(): mixed
    {
        if (!$this->valid()) {
            throw new RqlDriverError("No more data available.");
        }
        return $this->currentData[$this->currentIndex]->toNative($this->toNativeOptions);
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this as $val) {
            $result[] = $val;
        }
        return $result;
    }

    public function bufferedCount(): int
    {
        return $this->currentSize - $this->currentIndex;
    }

    public function getNotes(): array
    {
        return $this->notes;
    }

    public function __toString(): string
    {
        return "Cursor";
    }

    public function __destruct()
    {
        if ($this->connection->isOpen()) {
            // Cancel the request
            $this->close();
        }
    }
}
