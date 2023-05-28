<?php

namespace r\Options;

enum ReadMode: string implements \JsonSerializable
{
    case Single = 'single';
    case Majority = 'majority';
    case Outdated = 'outdated';

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}
