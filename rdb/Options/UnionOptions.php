<?php

namespace r\Options;

class UnionOptions
{
    /**
     * The optional interleave argument controls how the sequences will be merged:
     *
     * - true: results will be mixed together; this is the fastest setting, but ordering of elements is
     *   not guaranteed. (This is the default.)
     * - false: input sequences will be appended to one another, left to right.
     * - "field_name": a string will be taken as the name of a field to perform a merge-sort on. The input sequences
     *   must be ordered before being passed to union.
     * - function: the interleave argument can take a function whose argument is the current row, and whose return
     *   value is a value to perform a merge-sort on.
     * @param bool|string|\Closure $interleave
     */
    public function __construct(public readonly bool|string|\Closure $interleave)
    {
    }
}