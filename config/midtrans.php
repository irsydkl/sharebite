<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    | Your Midtrans server key. Use the Sandbox key for testing.
    | Get it from: https://dashboard.midtrans.com/settings/config_info
    */
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    | Used in the frontend (Snap.js) to initiate payment UI.
    */
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    | Set to false for Sandbox (testing), true for Production.
    */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Merchant ID
    |--------------------------------------------------------------------------
    */
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
];
