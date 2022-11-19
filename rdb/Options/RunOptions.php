<?php

namespace r\Options;

readonly class RunOptions
{
    public function __construct(
        public ReadMode|null $read_mode = null,
        public FormatMode|null $time_format = null,
        public bool|null $profile = null,
        public Durability|null $durability = null,
        public FormatMode|null $group_format = null,
        public bool|null $noreply = null,
        public string|null $db = null,
        public int|null $array_limit = null,
        public FormatMode|null $binary_format = null,
        public int|null $min_batch_rows = null,
        public int|null $max_batch_rows = null,
        public int|null $max_batch_bytes = null,
        public int|null $max_batch_seconds = null,
        public int|null $first_batch_scaledown_factor = null,
    ) {
    }
}
