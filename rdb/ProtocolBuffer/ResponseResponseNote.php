<?php

namespace r\ProtocolBuffer;

enum ResponseResponseNote: int
{
    case PB_ATOM_FEED = 2;
    case PB_INCLUDES_STATES = 5;
    case PB_ORDER_BY_LIMIT_FEED = 3;
    case PB_SEQUENCE_FEED = 1;
    case PB_UNIONED_FEED = 4;
}
