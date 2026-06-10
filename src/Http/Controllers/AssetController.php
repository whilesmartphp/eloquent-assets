<?php

namespace Whilesmart\Assets\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Whilesmart\Assets\Enums\AssetStatus;
use Whilesmart\Assets\Http\Requests\StoreAssetRequest;
use Whilesmart\Assets\Http\Requests\UpdateAssetRequest;
use Whilesmart\Assets\Http\Resources\AssetResource;
use Whilesmart\Assets\Models\Asset;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerController;

class AssetController extends Controller
{
    use AuthorizesOwnerController;

    public function index(Request $request): JsonResponse
    {
        $query = $this->scopeAccessibleOwners(Asset::query(), $request->user());

        if ($request->filled('owner_type') && $request->filled('owner_id')) {
            $query->where('owner_type', $request->input('owner_type'))
                ->where('owner_id', $request->input('owner_id'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('expiring_within')) {
            $query->expiringWithin((int) $request->input('expiring_within'));
        }

        if ($request->filled('q')) {
            $term = '%'.strtolower($request->input('q')).'%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('lower(name) like ?', [$term])
                    ->orWhereRaw('lower(reference) like ?', [$term]);
            });
        }

        $assets = $query->orderByDesc('updated_at')
            ->paginate((int) $request->input('per_page', 25));

        return response()->json([
            'success' => true,
            'data' => AssetResource::collection($assets)->response()->getData(true),
        ]);
    }

    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = Asset::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => new AssetResource($asset),
        ], 201);
    }

    public function show(Asset $asset, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($asset, $request->user());

        return response()->json([
            'success' => true,
            'data' => new AssetResource($asset),
        ]);
    }

    public function update(UpdateAssetRequest $request, Asset $asset): JsonResponse
    {
        $this->authorizeAccessTo($asset, $request->user());
        $asset->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => new AssetResource($asset->fresh()),
        ]);
    }

    public function destroy(Asset $asset, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($asset, $request->user());
        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset deleted.',
        ]);
    }

    public function renew(Asset $asset, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($asset, $request->user());

        $months = (int) ($request->input('months')
            ?? $asset->renew_interval_months
            ?? 12);

        $from = $asset->expires_at && $asset->expires_at->isFuture()
            ? $asset->expires_at
            : now();

        $asset->update([
            'expires_at' => $from->copy()->addMonths($months)->toDateString(),
            'status' => AssetStatus::InUse,
            'last_reminded_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'data' => new AssetResource($asset->fresh()),
        ]);
    }

    public function retire(Asset $asset, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($asset, $request->user());

        $asset->update(['status' => AssetStatus::Retired]);

        return response()->json([
            'success' => true,
            'data' => new AssetResource($asset->fresh()),
        ]);
    }
}
