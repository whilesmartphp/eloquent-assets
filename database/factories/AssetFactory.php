<?php

namespace Whilesmart\Assets\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Whilesmart\Assets\Enums\AssetCategory;
use Whilesmart\Assets\Enums\AssetStatus;
use Whilesmart\Assets\Models\Asset;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'category' => $this->faker->randomElement(AssetCategory::values()),
            'status' => AssetStatus::InUse->value,
            'value_cents' => $this->faker->numberBetween(10000, 5000000),
            'currency' => 'USD',
            'acquired_at' => $this->faker->date(),
        ];
    }

    public function expiring(int $inDays = 14): static
    {
        return $this->state(fn () => [
            'category' => AssetCategory::Domain->value,
            'expires_at' => now()->addDays($inDays)->toDateString(),
            'renew_interval_months' => 12,
        ]);
    }
}
