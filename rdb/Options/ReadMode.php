<?php

namespace r\Options;

enum ReadMode: string
{
    case Single = 'single';
    case Majority = 'majority';
    case Outdated = 'outdated';
}