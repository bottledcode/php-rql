<?php

namespace r\ProtocolBuffer;

enum QueryQueryType: int
{
    case PB_CONTINUE = 2;
    case PB_NOREPLY_WAIT = 4;
    case PB_SERVER_INFO = 5;
    case PB_START = 1;
    case PB_STOP = 3;
}
