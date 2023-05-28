<?php

namespace r\Options;

enum HttpResultFormat: string implements \JsonSerializable
{
    /**
     * always return a string.
     */
    case Text = 'text';

    /**
     * parse the result as JSON, raising an error on failure.
     */
    case Json = 'json';

    /**
     * parse the result as Padded JSON.
     */
    case JsonP = 'jsonp';

    /**
     * return a binary object.
     */
    case Binary = 'binary';

    /**
     * parse the result based on its Content-Type (the default)
     * - application/json: as json
     * - application/json-p, text/json-p, text/javascript: as jsonp
     * - audio/*, video/*, image/*, application/octet-stream: as binary
     * - anything else: as text
     */
    case Auto = 'auto';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
