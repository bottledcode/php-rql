<?php

namespace r\Options;

class CircleOptions
{
    public function __construct(
        public readonly float|null $num_vertices = null,
        public readonly bool|null $geo_system = null,
        public readonly bool|null $fill = null,
        public readonly string|null $unit = null,
    ) {
    }
}