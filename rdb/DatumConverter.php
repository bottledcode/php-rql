<?php

namespace r;

use DateTimeInterface;
use JsonSerializable;
use r\Datum\ArrayDatum;
use r\Datum\BoolDatum;
use r\Datum\Datum;
use r\Datum\NullDatum;
use r\Datum\NumberDatum;
use r\Datum\ObjectDatum;
use r\Datum\StringDatum;
use r\Exceptions\RqlDriverError;
use r\FunctionQuery\RFunction;
use r\Queries\Dates\Iso8601;
use r\ValuedQuery\MakeArray;
use r\ValuedQuery\MakeObject;
use r\ValuedQuery\RVar;
use ReflectionFunction;

class DatumConverter
{
    public static function decodedJSONToDatum(
        array|\stdClass|int|float|string|null|bool $json
    ): ObjectDatum|StringDatum|BoolDatum|NumberDatum|NullDatum|ArrayDatum {
        if (is_null($json)) {
            return NullDatum::decodeServerResponse($json);
        }
        if (is_bool($json)) {
            return BoolDatum::decodeServerResponse($json);
        }
        if (is_int($json) || is_float($json)) {
            return NumberDatum::decodeServerResponse($json);
        }
        if (is_string($json)) {
            return StringDatum::decodeServerResponse($json);
        }
        if (is_array($json) && array_is_list($json)) {
            return ArrayDatum::decodeServerResponse($json);
        }
        if (is_array($json)) {
            return ObjectDatum::decodeServerResponse($json);
        }
        if (is_object($json)) {
            return ObjectDatum::decodeServerResponse($json);
        }

        throw new RqlDriverError("Unhandled type " . get_class($json));
    }

    public function tryEncodeAsJson(mixed $v): bool|string
    {
        if ($this->canEncodeAsJson($v)) {
            // PHP by default loses some precision when encoding floats, so we temporarily
            // bump up the `precision` option to avoid this.
            // The 17 assumes IEEE-754 double precision numbers.
            // Source: http://docs.oracle.com/cd/E19957-01/806-3568/ncg_goldberg.html
            //         "The same argument applied to double precision shows that 17 decimal
            //          digits are required to recover a double precision number."
            $previousPrecision = ini_set("precision", 17);
            $json = json_encode($v);
            if ($previousPrecision !== false) {
                ini_set("precision", $previousPrecision);
            }
            if ($json === false) {
                throw new RqlDriverError("Failed to encode document as JSON: " . json_last_error());
            }
            return $json;
        } else {
            return false;
        }
    }

    public function canEncodeAsJson(mixed $v): bool
    {
        if (is_null($v) || is_bool($v) || is_int($v) || is_float($v) || is_string($v)) {
            return true;
        }

        if (is_array($v)) {
            foreach ($v as $key => $val) {
                if (!is_numeric($key) && !is_string($key)) {
                    return false;
                }
                if (!$this->canEncodeAsJson($val)) {
                    return false;
                }
            }
            return true;
        }

        if (is_object($v) && is_subclass_of($v, JsonSerializable::class)) {
            return true;
        }

        return false;
    }

    public function nativeToDatumOrFunction(mixed $f, bool $wrapImplicit = true): mixed
    {
        if (!(is_object($f) && is_subclass_of($f, Query::class))) {
            try {
                $f = $this->nativeToDatum($f);
                if (!is_subclass_of($f, Datum::class)) {
                    // $f is not a simple datum. Wrap it into a function:
                    $f = new RFunction(array(new RVar('_')), $f);
                }
            } catch (RqlDriverError $e) {
                $f = $this->nativeToFunction($f);
            }
        }
        if ($wrapImplicit) {
            return $this->wrapImplicitVar($f);
        } else {
            return $f;
        }
    }

    public function nativeToDatum(array|object|null|bool|int|float|string $v
    ): MakeObject|ObjectDatum|Iso8601|MakeArray|StringDatum|BoolDatum|NumberDatum|\r\Query|NullDatum|ArrayDatum {
        if (is_array($v) || (is_object($v) && in_array(get_class($v), array(\stdClass::class, \ArrayObject::class)))) {
            $datumArray = array();
            $hasNonNumericKey = false;
            $mustUseMakeTerm = false;
            if (is_object($v)) {
                // Handle "stdClass" objects
                $hasNonNumericKey = true; // Force conversion into an ObjectDatum
                $v = (array)$v;
            }
            foreach ($v as $key => $val) {
                if (!is_numeric($key) && !is_string($key)) {
                    throw new RqlDriverError("Key must be a string.");
                }
                if (is_subclass_of($val, \r\Query::class) && !is_subclass_of($val, Datum::class)) {
                    $subDatum = $val;
                    $mustUseMakeTerm = true;
                } else {
                    $subDatum = $this->nativeToDatum($val);
                    if (!is_subclass_of($subDatum, Datum::class)) {
                        $mustUseMakeTerm = true;
                    }
                }
                if (is_string($key)) {
                    $hasNonNumericKey = true;
                    $datumArray[$key] = $subDatum;
                } else {
                    $datumArray[$key] = $subDatum;
                }
            }

            // Note: In the case of $hasNonNumericKey === false, we cannot
            //   know if we should convert to an array or an object. We
            //   currently assume array, but this is not overly clean.
            //   Of course the user always has the option to wrap data
            //   into a Datum manually.
            //   We use this behavior because it is consistent to json_encode,
            //   which we sometimes use as a transparent replacement for
            //   $this->nativeToDatum().
            if ($hasNonNumericKey) {
                if ($mustUseMakeTerm) {
                    return new MakeObject($datumArray);
                }

                return new ObjectDatum($datumArray);
            }

            if ($mustUseMakeTerm) {
                return new MakeArray($datumArray);
            }

            return new ArrayDatum($datumArray);
        }

        if (is_null($v)) {
            return new NullDatum();
        }

        if (is_bool($v)) {
            return new BoolDatum($v);
        }

        if (is_int($v) || is_float($v)) {
            return new NumberDatum($v);
        }

        if (is_string($v)) {
            return new StringDatum($v);
        }

        if (is_object($v) && is_subclass_of($v, Query::class)) {
            return $v;
        }

        if (is_object($v) && (is_subclass_of($v, \DateTimeInterface::class))) {
            // PHP prior to 5.5.0 doens't have DateTimeInterface, so we test for DateTime directly as well ^^^^^
            $iso8601 = $v->format(DateTimeInterface::ATOM);
            return new Iso8601($iso8601);
        }

        if(is_object($v) && class_implements($v, JsonSerializable::class)) {
            return $this->nativeToDatum($v->jsonSerialize());
        }

        throw new RqlDriverError("Unhandled type " . get_class($v));
    }

    public function nativeToFunction(Query|callable $f): RFunction|\r\Query
    {
        if (is_object($f) && is_subclass_of($f, Query::class)) {
            return $this->wrapImplicitVar($f);
        }

        $reflection = new ReflectionFunction($f);

        $args = array();
        foreach ($reflection->getParameters() as $param) {
            $args[] = new RVar($param->getName());
        }
        $result = $reflection->invokeArgs($args);

        if (!(is_object($result) && is_subclass_of($result, Query::class))) {
            if (!isset($result)) {
                // In case of null, assume that the user forgot to add a return.
                // If null is the intended value, r\expr() should be wrapped around the return value.
                throw new RqlDriverError(
                    "The function did not evaluate to a value (missing return?). If the function is intended to return `null,` please use `return r\expr(null);`."
                );
            } else {
                $result = $this->nativeToDatum($result);
            }
        }

        return new RFunction($args, $result);
    }

    public function wrapImplicitVar(Query $q): RFunction|\r\Query
    {
        if ($q->hasUnwrappedImplicitVar()) {
            return new RFunction(array(new RVar('_')), $q);
        } else {
            return $q;
        }
    }
}
