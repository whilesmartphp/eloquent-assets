<?php

namespace Whilesmart\Assets\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Whilesmart\Assets\Database\Factories\AssetFactory;
use Whilesmart\Assets\Enums\AssetStatus;
use Whilesmart\Files\Traits\HasFiles;

class Asset extends Model
{
    use HasFactory, HasFiles, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => AssetStatus::class,
        'acquired_at' => 'date',
        'expires_at' => 'date',
        'auto_renew' => 'boolean',
        'last_reminded_at' => 'datetime',
        'value_cents' => 'integer',
        'useful_life_months' => 'integer',
        'renew_interval_months' => 'integer',
        'details' => 'array',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {
            if (empty($asset->reference)) {
                $asset->reference = static::generateReference($asset);
            }
        });
    }

    /**
     * Next per-owner asset reference, e.g. AST-00001. Bumps past any
     * reference already taken for this owner so the (owner, reference)
     * unique holds.
     */
    public static function generateReference(Asset $asset): string
    {
        $prefix = (string) config('assets.reference_prefix', 'AST-');
        $base = static::withTrashed()
            ->where('owner_type', $asset->owner_type)
            ->where('owner_id', $asset->owner_id);

        $seq = (clone $base)->count();

        do {
            $seq++;
            $reference = $prefix.str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
        } while ((clone $base)->where('reference', $reference)->exists());

        return $reference;
    }

    public function getTable(): string
    {
        return config('assets.assets_table', 'assets');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeExpiringWithin(Builder $query, int $days): Builder
    {
        return $query->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', now()->addDays($days));
    }

    public function daysUntilExpiry(): ?int
    {
        if ($this->expires_at === null) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->expires_at->startOfDay(), false);
    }

    protected static function newFactory(): AssetFactory
    {
        return AssetFactory::new();
    }
}
