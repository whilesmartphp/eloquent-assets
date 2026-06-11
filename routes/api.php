<?php

use Illuminate\Support\Facades\Route;
use Whilesmart\Assets\Http\Controllers\AssetController;

Route::apiResource('assets', AssetController::class);
Route::post('assets/{asset}/renew', [AssetController::class, 'renew']);
Route::post('assets/{asset}/retire', [AssetController::class, 'retire']);
