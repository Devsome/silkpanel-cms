<?php

namespace App\Enums;

enum DatabaseNameEnums: string
{
    case SRO_SHARD = 'sro_shard';
    case SRO_ACCOUNT = 'sro_account';
    case SRO_LOG = 'sro_log';
    case SRO_CUSTOM = 'sro_custom';
    case SRO_PORTAL = 'sro_portal';
}
