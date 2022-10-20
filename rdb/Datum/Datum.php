<?php

namespace r\Datum;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;
use r\Exceptions\RqlDriverError;

abstract class Datum extends ValuedQuery
{
    private mixed $value;

    public function __construct($value = null)
    {
        if (isset($value)) {
            $this->setValue($value);
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_DATUM;
    }

    public function toNative(array $opts): array|string|object|null|float|bool|int
    {
        return $this->getValue();
    }

    public function __toString(): string
    {
        return "" . $this->getValue();
    }

    public function toString(&$backtrace): string
    {
        $result = $this->__toString();
        if (is_null($backtrace)) {
            return $result;
        } else {
            if ($backtrace === false) {
                return str_repeat(" ", strlen($result));
            }
            $backtraceFrame = $backtrace->consumeFrame();
            if ($backtraceFrame !== false) {
                throw new RqlDriverError(
                    "Internal Error: The backtrace says that we should have an argument in a Datum. "
                    . "This is not possible."
                );
            }
            return str_repeat("~", strlen($result));
        }
    }

    public function getValue(): array|string|object|null|int|float|bool
    {
        return $this->value;
    }

    public function setValue(array|string|object|null|int|float|bool $val): void
    {
        $this->value = $val;
    }
}
