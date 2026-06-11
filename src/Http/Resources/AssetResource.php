<?php

namespace Whilesmart\Assets\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'reference' => $this->reference,
            'name' => $this->name,
            'category' => $this->category,
            'status' => $this->status?->value,
            'value_cents' => (int) $this->value_cents,
            'currency' => $this->currency,
            'acquired_at' => $this->acquired_at?->toDateString(),
            'useful_life_months' => $this->useful_life_months,
            'assignee_type' => $this->assignee_type,
            'assignee_id' => $this->assignee_id,
            'location' => $this->location,
            'expires_at' => $this->expires_at?->toDateString(),
            'days_until_expiry' => $this->daysUntilExpiry(),
            'auto_renew' => (bool) $this->auto_renew,
            'renew_interval_months' => $this->renew_interval_months,
            'last_reminded_at' => $this->last_reminded_at?->toIso8601String(),
            'details' => $this->details ?? [],
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}
