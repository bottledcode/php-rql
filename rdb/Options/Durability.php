<?php

namespace r\Options;

use PHPUnit\Util\Json;

enum Durability: string implements \JsonSerializable {
    case Hard = 'hard';
    case Soft = 'soft';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
