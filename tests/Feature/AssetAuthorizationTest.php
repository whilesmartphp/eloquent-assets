<?php

namespace Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Whilesmart\Assets\Models\Asset;
use Whilesmart\OwnerAccess\Contracts\OwnerAuthorizer;

class AssetAuthorizationTest extends TestCase
{
    private const OWNER = 'App\\Models\\Workspace';

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(OwnerAuthorizer::class, new class implements OwnerAuthorizer
        {
            public function authorize(?Authenticatable $user, string $ownerType, mixed $ownerId): bool
            {
                return false;
            }

            public function scope(Builder $query, ?Authenticatable $user, string $ownerTypeColumn = 'owner_type', string $ownerIdColumn = 'owner_id'): Builder
            {
                return $query->whereRaw('0 = 1');
            }
        });
    }

    #[Test]
    public function store_is_forbidden_when_authorizer_denies(): void
    {
        $this->postJson('/api/assets', [
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'name' => 'Hijacked',
            'category' => 'equipment',
        ])->assertForbidden();

        $this->assertDatabaseCount('assets', 0);
    }

    #[Test]
    public function show_update_destroy_and_actions_are_forbidden_when_authorizer_denies(): void
    {
        $asset = Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'Private', 'category' => 'equipment']);

        $this->getJson("/api/assets/{$asset->id}")->assertForbidden();
        $this->putJson("/api/assets/{$asset->id}", ['name' => 'Hijacked'])->assertForbidden();
        $this->postJson("/api/assets/{$asset->id}/renew")->assertForbidden();
        $this->postJson("/api/assets/{$asset->id}/retire")->assertForbidden();
        $this->deleteJson("/api/assets/{$asset->id}")->assertForbidden();

        $this->assertSame('Private', $asset->fresh()->name);
    }

    #[Test]
    public function index_returns_nothing_when_scope_denies(): void
    {
        Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'Private', 'category' => 'equipment']);

        $this->getJson('/api/assets')
            ->assertOk()
            ->assertJsonPath('data.meta.total', 0);
    }
}
