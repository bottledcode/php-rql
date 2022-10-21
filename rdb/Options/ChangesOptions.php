<?php

namespace r\Options;

class ChangesOptions
{
    public function __construct(
        public readonly bool|float|null $squash = null,
        public readonly int|null $changefeed_queue_size = null,
        public readonly bool|null $include_initial = null,
        public readonly bool|null $include_states = null,
        public readonly bool|null $include_offsets = null,
        public readonly bool|null $include_types = null,
    ) {
    }
}