<?php

namespace r\Options;

enum HttpMethod: string implements \JsonSerializable {
    case Get = 'GET';
    case Put = 'PUT';
    case Post = 'POST';
    case Patch = 'PATCH';
    case Delete = 'DELETE';
    case Head = 'HEAD';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
