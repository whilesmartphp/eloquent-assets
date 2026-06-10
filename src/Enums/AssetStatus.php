<?php

namespace Whilesmart\Assets\Enums;

enum AssetStatus: string
{
    case InUse = 'in_use';
    case Assigned = 'assigned';
    case InStock = 'in_stock';
    case Maintenance = 'maintenance';
    case Expiring = 'expiring';
    case Expired = 'expired';
    case Retired = 'retired';
    case Disposed = 'disposed';
}
