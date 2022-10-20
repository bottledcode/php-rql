<?php

namespace r;

class ConnectionOptions
{
    /**
     * @param string $host The host to connect to
     * @param int $port The port to connect to
     * @param string|null $db The default db to use
     * @param string $user The username
     * @param string|null $password The password or API key
     * @param int|float|null $timeout The timeout
     * @param mixed|null $ssl SSL options
     */
    public function __construct(
        public readonly string $host = 'localhost',
        public readonly int $port = 28015,
        public readonly string|null $db = null,
        public readonly string $user = 'admin',
        public readonly string $password = '',
        public readonly int|float|null $timeout = null,
        public readonly mixed $ssl = null,
    ) {
    }
}