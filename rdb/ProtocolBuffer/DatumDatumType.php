<?php

namespace r\ProtocolBuffer;

enum DatumDatumType: int
{
    case PB_R_ARRAY = 5;
    case PB_R_BOOL = 2;
    case PB_R_JSON = 7;
    case PB_R_NULL = 1;
    case PB_R_NUM = 3;
    case PB_R_OBJECT = 6;
    case PB_R_STR = 4;
}
