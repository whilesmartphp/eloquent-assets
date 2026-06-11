<?php

namespace Whilesmart\Assets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerRequest;

class StoreAssetRequest extends FormRequest
{
    use AuthorizesOwnerRequest;

    public function authorize(): bool
    {
        return $this->authorizeOwnerInRequest();
    }

    public function rules(): array
    {
        return [
            'owner_type' => ['required', 'string'],
            'owner_id' => ['required'],
            'reference' => ['nullable', 'string', 'max:60'],
            'name' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:60'],
            'status' => ['nullable', 'string', 'max:40'],
            'value_cents' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'acquired_at' => ['nullable', 'date'],
            'useful_life_months' => ['nullable', 'integer', 'min:0'],
            'assignee_type' => ['nullable', 'string', 'required_with:assignee_id'],
            'assignee_id' => ['nullable', 'required_with:assignee_type'],
            'location' => ['nullable', 'string', 'max:200'],
            'expires_at' => ['nullable', 'date'],
            'auto_renew' => ['nullable', 'boolean'],
            'renew_interval_months' => ['nullable', 'integer', 'min:1'],
            'details' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
