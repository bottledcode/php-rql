<?php

namespace r\Options;

class HttpOptions
{
    /**
     * @param int|null $timeout timeout period in seconds to wait before aborting the connect (default 30).
     * @param int|null $attempts number of retry attempts to make after failed connections (default 5).
     * @param int|null $redirects number of redirect and location headers to follow (default 1).
     * @param bool|null $verify if true, verify the server’s SSL certificate (default true).
     * @param HttpResultFormat|null $resultFormat the format to return results in.
     * @param HttpMethod|null $method HTTP method to use for the request.
     * @param array|null $auth object giving authentication, with the following fields: type (basic or digest), username, and password.
     * @param array|null $params URL parameters to append to the URL as encoded key/value pairs
     * @param array|null $header Extra header lines to include. The value may be an array of strings
     * @param string|array|null $data Data to send to the server on a POST, PUT, PATCH, or DELETE request. For POST requests, data may be either an array (which will be written to the body as form-encoded key/value pairs) or a string; for all other requests, data will be serialized as JSON and placed in the request body, sent as Content-Type: application/json
     */
    public function __construct(
        public readonly int|null $timeout = null,
        public readonly int|null $attempts = null,
        public readonly int|null $redirects = null,
        public readonly bool|null $verify = null,
        public readonly HttpResultFormat|null $result_format = null,
        public readonly HttpMethod|null $method = null,
        public readonly array|null $auth = null,
        public readonly array|null $params = null,
        public readonly array|null $header = null,
        public readonly string|array|null $data = null,
    ) {
    }
}