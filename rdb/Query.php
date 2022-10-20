<?php

namespace r;

use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\Info;
use r\Queries\Control\RDefault;
use r\Exceptions\RqlDriverError;
use r\DatumConverter;

abstract class Query extends DatumConverter
{
    private array $positionalArgs = [];
    private array $optionalArgs = [];
    private bool $unwrappedImplicitVar = false;

    abstract protected function getTermType(): TermTermType;

    protected function setOptionalArg(string $key, Query $val): void
    {
        if ($val->hasUnwrappedImplicitVar()) {
            $this->unwrappedImplicitVar = true;
        }
        $this->optionalArgs[$key] = $val;
    }

    protected function setPositionalArg(int $pos, Query $arg): void
    {
        if ($arg->hasUnwrappedImplicitVar()) {
            $this->unwrappedImplicitVar = true;
        }
        $this->positionalArgs[$pos] = $arg;
    }

    public function hasUnwrappedImplicitVar(): bool
    {
        return $this->unwrappedImplicitVar;
    }

    public function encodeServerRequest(): mixed
    {
        $args = array();
        foreach ($this->positionalArgs as $i => $arg) {
            $args[] = $arg->encodeServerRequest();
        }
        $optargs = array();
        foreach ($this->optionalArgs as $key => $val) {
            $optargs[$key] = $val->encodeServerRequest();
        }
        return array($this->getTermType(), $args, (object)$optargs);
    }

    public function run(Connection $connection, $options = null): Cursor|array|string|null|\DateTimeInterface|float|int|bool
    {
        return $connection->run($this, $options, $profile);
    }

    public function profile(Connection $connection, array $options = [], Cursor|array|string|null &$result = null)
    {
        $options['profile'] = true;
        $result = $connection->run($this, $options, $profile);
        return $profile;
    }

    public function info(): Info
    {
        return new Info($this);
    }
    public function rDefault($defaultCase): RDefault
    {
        return new RDefault($this, $defaultCase);
    }

    public function __toString(): string
    {
        $backtrace = null;
        return $this->toString($backtrace);
    }

    public function toString(&$backtrace): string
    {
        // TODO (daniel): This kind of printing backtraces is pretty hacky. Overhaul this.
        //  Maybe we could generate a PHP backtrace structure...

        $backtraceFrame = null;
        if (isset($backtrace) && $backtrace !== false) {
            $backtraceFrame = $backtrace->consumeFrame();
        }

        $type = $this->getTermType()->name;

        $argList = "";
        foreach ($this->positionalArgs as $i => $arg) {
            if ($i > 0) {
                if (isset($backtrace)) {
                    $argList .= "  ";
                } else {
                    $argList .= ", ";
                }
            }

            $subTrace = is_null($backtrace) ? null : false;
            if (is_object($backtraceFrame)
                && $backtraceFrame->isPositionalArg()
                && $backtraceFrame->getPositionalArgPosition() == $i
            ) {
                $subTrace = $backtrace;
            }
            $argList .= $arg->toString($subTrace);
        }

        $optArgList = "";
        $firstOptArg = true;
        foreach ($this->optionalArgs as $key => $val) {
            if (!$firstOptArg) {
                if (isset($backtrace)) {
                    $optArgList .= "  ";
                } else {
                    $optArgList .= ", ";
                }
            }
            $firstOptArg = false;

            $subTrace = is_null($backtrace) ? null : false;
            if (is_object($backtraceFrame)
                && $backtraceFrame->isOptionalArg()
                && $backtraceFrame->getOptionalArgName() == $key
            ) {
                $subTrace = $backtrace;
            }
            if (isset($backtrace)) {
                $optArgList .= str_repeat(" ", strlen($key)) . "    " . $val->toString($subTrace);
            } else {
                $optArgList .= $key . " => " . $val->toString($subTrace);
            }
        }

        if ($optArgList) {
            if (strlen($argList) > 0) {
                if (isset($backtrace)) {
                    $argList .= "  ";
                } else {
                    $argList .= ", ";
                }
            }
            if (isset($backtrace)) {
                $argList .= "        " . $optArgList . " ";
            } else {
                $argList .= "OptArgs(" . $optArgList . ")";
            }
        }

        $result = $type . "(" . $argList . ")";
        if (isset($backtrace)) {
            if ($backtraceFrame === false) {
                // We are the origin of the trouble
                return str_repeat("~", strlen($result));
            } else {
                return str_repeat(" ", strlen($type)) . " " . $argList . " ";
            }
        } else {
            return $result;
        }
    }
}
