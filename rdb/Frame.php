<?php

namespace r;

class Frame
{
    private bool $isPositionalArg = false;
    private bool $isOptionalArg = false;
    private string|null $optionalArgName = null;
    private int|null $positionalArgPosition = null;

    public static function decodeServerResponse(string|int $frame): Frame
    {
        $result = new Frame();
        if (is_string($frame)) {
            $result->isOptionalArg = true;
            $result->optionalArgName = $frame;
        } else {
            $result->isPositionalArg = true;
            $result->positionalArgPosition = $frame;
        }

        return $result;
    }

    public function isPositionalArg(): bool
    {
        return $this->isPositionalArg;
    }

    public function isOptionalArg(): bool
    {
        return $this->isOptionalArg;
    }

    public function getOptionalArgName(): bool
    {
        return $this->optionalArgName;
    }

    public function getPositionalArgPosition(): bool
    {
        return $this->positionalArgPosition;
    }
}
