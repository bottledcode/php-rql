<?php

namespace r;

class Backtrace
{
    private array $frames = [];

    public static function decodeServerResponse(mixed $backtrace): Backtrace
    {
        $result = new Backtrace();
        $result->frames = [];
        foreach ($backtrace as $frame) {
            $result->frames[] = Frame::decodeServerResponse($frame);
        }
        return $result;
    }

    /**
     * Returns true if no more frames are available
     */
    public function consumeFrame(): bool|Frame|Backtrace
    {
        if (\count($this->frames) == 0) {
            return false;
        }
        $frame = $this->frames[0];
        $this->frames = array_slice($this->frames, 1);
        return $frame;
    }
}
