<?php

return [

    /*
    |--------------------------------------------------------------------------
    | License Manager integration
    |--------------------------------------------------------------------------
    |
    | The marketplace provisions licenses through the License Manager's
    | POST /api/licenses endpoint (see API.md in winner-license-application).
    | The token is server-side only and must never be rendered in a page,
    | log, or email.
    |
    */

    'license_manager' => [
        'url' => env('LICENSE_MANAGER_URL'),
        'token' => env('LICENSE_MANAGER_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SSLCommerz payment gateway
    |--------------------------------------------------------------------------
    |
    | Used from Phase 3 onward. Sandbox mode stays on until the live store
    | credentials are approved.
    |
    */

    'sslcommerz' => [
        'store_id' => env('SSLCZ_STORE_ID'),
        'store_password' => env('SSLCZ_STORE_PASSWORD'),
        'sandbox' => env('SSLCZ_SANDBOX', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Backups
    |--------------------------------------------------------------------------
    */

    'backup' => [
        // Full path when the binary isn't on PATH (e.g. XAMPP:
        // D:\xampp\mysql\bin\mysqldump.exe).
        'mysqldump_path' => env('MYSQLDUMP_PATH', 'mysqldump'),
    ],

];
