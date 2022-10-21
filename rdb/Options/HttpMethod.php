<?php

namespace r\Options;

enum HttpMethod: string {
    case Get = 'GET';
    case Put = 'PUT';
    case Post = 'POST';
    case Patch = 'PATCH';
    case Delete = 'DELETE';
    case Head = 'HEAD';
}