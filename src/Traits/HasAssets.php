<?php

namespace Whilesmart\Assets\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Whilesmart\Assets\Models\Asset;

trait HasAssets
{
    public function assets(): MorphMany
    {
        return $this->morphMany(Asset::class, 'owner');
    }
}
