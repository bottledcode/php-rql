<?php

namespace r\Options;

enum FormatMode: string implements \JsonSerializable {
    case Native = 'native';
    case Raw = 'raw';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
