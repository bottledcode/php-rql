<?php

namespace r\ProtocolBuffer;

enum ResponseResponseType: int
{
    case PB_CLIENT_ERROR = 16;
    case PB_COMPILE_ERROR = 17;
    case PB_RUNTIME_ERROR = 18;
    case PB_SERVER_INFO = 5;
    case PB_SUCCESS_ATOM = 1;
    case PB_SUCCESS_PARTIAL = 3;
    case PB_SUCCESS_SEQUENCE = 2;
    case PB_WAIT_COMPLETE = 4;
}
