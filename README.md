# whilesmart/eloquent-assets

A polymorphic asset register for Laravel: one model for everything a business owns, whether
physical (equipment, vehicles) or digital (domains, software licenses, subscriptions). Each
asset is scoped to an owner (a workspace, organization, user) through
[`whilesmart/eloquent-owner-access`](https://github.com/whilesmartphp/eloquent-owner-access),
carries a value, and optionally a renewal/expiry date.

## Install

```
composer require whilesmart/eloquent-assets
php artisan migrate
```

Routes register automatically under the `api` prefix with `auth:sanctum`. Set
`ASSETS_REGISTER_ROUTES=false` to mount them yourself.

## Owning model

Add the trait to whatever owns assets:

```php
use Whilesmart\Assets\Traits\HasAssets;

class Workspace extends Model
{
    use HasAssets;
}

$workspace->assets()->create([
    'name' => 'acme.com',
    'category' => 'domain',
    'value_cents' => 1500,
    'currency' => 'USD',
    'expires_at' => now()->addYear(),
    'renew_interval_months' => 12,
    'details' => ['registrar' => 'Namecheap', 'auto_renew' => true],
]);
```

`reference` (e.g. `AST-00001`) is generated per owner when omitted. Category-specific fields
live in the `details` JSON column; common fields (value, status, expiry) are columns.

## Endpoints

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/assets` | List (filter by `category`, `status`, `expiring_within`, `q`) |
| POST | `/api/assets` | Create |
| GET | `/api/assets/{asset}` | Show |
| PUT/PATCH | `/api/assets/{asset}` | Update |
| DELETE | `/api/assets/{asset}` | Soft delete |
| POST | `/api/assets/{asset}/renew` | Advance `expires_at` by `months` (or `renew_interval_months`) |
| POST | `/api/assets/{asset}/retire` | Mark retired |

## Renewals

`Asset::query()->expiringWithin(30)` returns assets due within 30 days. Host apps can run a
scheduled sweep over this scope to send renewal reminders.

## Status & category

`AssetStatus`: `in_use`, `assigned`, `in_stock`, `maintenance`, `expiring`, `expired`,
`retired`, `disposed`. `AssetCategory` lists the suggested categories, but `category` is a
plain string so new categories need no schema change.
