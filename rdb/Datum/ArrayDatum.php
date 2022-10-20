<?php

namespace r\Datum;

use r\DatumConverter;
use r\Exceptions\RqlDriverError;
use r\ValuedQuery\MakeArray;

class ArrayDatum extends Datum
{
    public static function decodeServerResponse(mixed $json): ArrayDatum
    {
        $jsonArray = array_values((array)$json);
        foreach ($jsonArray as &$val) {
            $val = DatumConverter::decodedJSONToDatum($val);
            unset($val);
        }
        $result = new ArrayDatum();
        $result->setValue($jsonArray);
        return $result;
    }

    public function setValue(array|object|string|null|float|bool|int $val): void
    {
        if (!is_array($val)) {
            throw new RqlDriverError("Not an array: " . $val);
        }
        foreach ($val as $v) {
            if (!(is_object($v) && is_subclass_of($v, "\\r\\Query"))) {
                throw new RqlDriverError("Not a Query: " . $v);
            }
        }
        parent::setValue($val);
    }

    public function encodeServerRequest(): array
    {
        $term = new MakeArray(array_values($this->getValue()));
        return $term->encodeServerRequest();
    }

    public function toNative(array $opts): array
    {
        $native = array();
        foreach ($this->getValue() as $val) {
            $native[] = $val->toNative($opts);
        }
        return $native;
    }

    public function __toString(): string
    {
        $string = 'array(';
        $first = true;
        foreach ($this->getValue() as $val) {
            if (!$first) {
                $string .= ", ";
            }
            $first = false;
            $string .= $val;
        }
        $string .= ')';
        return $string;
    }
}
