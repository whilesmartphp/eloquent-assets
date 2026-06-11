<?php

namespace Whilesmart\Assets\Enums;

/**
 * Suggested categories used for validation and the UI catalog. The model
 * stores `category` as a plain string, so host apps can introduce new
 * categories without a schema or enum change.
 */
enum AssetCategory: string
{
    case Equipment = 'equipment';
    case Domain = 'domain';
    case SoftwareLicense = 'software_license';
    case Subscription = 'subscription';
    case Vehicle = 'vehicle';

    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }
}
