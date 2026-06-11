<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Whilesmart\Assets\Models\Asset;

class AssetApiTest extends TestCase
{
    private const OWNER = 'App\\Models\\Workspace';

    #[Test]
    public function it_creates_an_asset_and_generates_a_reference(): void
    {
        $this->postJson('/api/assets', [
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'name' => 'acme.com',
            'category' => 'domain',
            'value_cents' => 1500,
            'currency' => 'USD',
            'expires_at' => '2027-01-01',
            'renew_interval_months' => 12,
            'details' => ['registrar' => 'Namecheap'],
        ])->assertCreated()
            ->assertJsonPath('data.reference', 'AST-00001')
            ->assertJsonPath('data.category', 'domain')
            ->assertJsonPath('data.details.registrar', 'Namecheap');

        $this->assertDatabaseHas('assets', [
            'owner_id' => 1,
            'name' => 'acme.com',
            'reference' => 'AST-00001',
        ]);
    }

    #[Test]
    public function it_filters_assets_by_owner_and_category(): void
    {
        Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'Laptop', 'category' => 'equipment']);
        Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'acme.com', 'category' => 'domain']);

        $this->getJson('/api/assets?owner_type='.urlencode(self::OWNER).'&owner_id=1&category=domain')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.name', 'acme.com');
    }

    #[Test]
    public function it_renews_an_asset_by_advancing_the_expiry(): void
    {
        $asset = Asset::create([
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'name' => 'acme.com',
            'category' => 'domain',
            'status' => 'expiring',
            'expires_at' => '2027-01-01',
            'renew_interval_months' => 12,
        ]);

        $this->postJson("/api/assets/{$asset->id}/renew")
            ->assertOk()
            ->assertJsonPath('data.expires_at', '2028-01-01')
            ->assertJsonPath('data.status', 'in_use');
    }

    #[Test]
    public function it_lists_assets_expiring_within_a_window(): void
    {
        Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'soon', 'category' => 'domain', 'expires_at' => now()->addDays(10)->toDateString()]);
        Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'later', 'category' => 'domain', 'expires_at' => now()->addDays(100)->toDateString()]);

        $this->getJson('/api/assets?expiring_within=30')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.name', 'soon');
    }

    #[Test]
    public function it_retires_an_asset(): void
    {
        $asset = Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'Laptop', 'category' => 'equipment']);

        $this->postJson("/api/assets/{$asset->id}/retire")
            ->assertOk()
            ->assertJsonPath('data.status', 'retired');
    }
}
