<?php

namespace r\ProtocolBuffer;

enum VersionDummyProtocol: int
{
    case PB_JSON = 0x7e6970c7;
    case PB_PROTOBUF = 0x271ffc41;
}
