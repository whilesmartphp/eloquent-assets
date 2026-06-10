<?php

return [
    'register_routes' => env('ASSETS_REGISTER_ROUTES', true),
    'route_prefix' => env('ASSETS_ROUTE_PREFIX', 'api'),
    'route_middleware' => ['api', 'auth:sanctum'],
    'assets_table' => env('ASSETS_TABLE', 'assets'),
    'reference_prefix' => env('ASSET_REFERENCE_PREFIX', 'AST-'),

    // How many days before an asset's expiry a renewal reminder should fire.
    'reminder_lead_days' => (int) env('ASSET_REMINDER_LEAD_DAYS', 30),
];
