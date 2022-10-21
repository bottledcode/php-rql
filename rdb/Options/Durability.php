<?php

namespace r\Options;

enum Durability: string {
    case Hard = 'hard';
    case Soft = 'soft';
}