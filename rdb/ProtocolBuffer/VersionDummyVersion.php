<?php

namespace r\ProtocolBuffer;

enum VersionDummyVersion: int
{
    case PB_V0_1 = 0x3f61ba36;
    case PB_V0_2 = 0x723081e1;
    case PB_V0_3 = 0x5f75e83e;
    case PB_V0_4 = 0x400c2d20;
    case PB_V1_0 = 0x34c2bdc3;
}
