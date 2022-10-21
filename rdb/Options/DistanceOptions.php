<?php

namespace r\Options;

class DistanceOptions
{
    public function __construct(
        public readonly string|null $unit = null,
        public readonly string|null $geo_system = null
    ) {
    }
}