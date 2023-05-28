<?php

namespace r\Options;

enum IdentifierFormat: string implements \JsonSerializable
{
    case Name = 'name';
    case Uuid = 'uuid';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
