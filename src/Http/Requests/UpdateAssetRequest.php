<?php

namespace Whilesmart\Assets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerRequest;

class UpdateAssetRequest extends FormRequest
{
    use AuthorizesOwnerRequest;

    public function authorize(): bool
    {
        return $this->authorizeOwnerOfBoundModel('asset');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:200'],
            'category' => ['sometimes', 'string', 'max:60'],
            'status' => ['nullable', 'string', 'max:40'],
            'value_cents' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'acquired_at' => ['nullable', 'date'],
            'useful_life_months' => ['nullable', 'integer', 'min:0'],
            'assigned_to' => ['nullable', 'string', 'max:200'],
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
