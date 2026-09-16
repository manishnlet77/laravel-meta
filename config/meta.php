<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Meta Graph API Version
    |--------------------------------------------------------------------------
    |
    | This specifies the default Graph API version to use for requests.
    | Ensure this version is supported by the package.
    |
    */
    'graph_version' => env('META_GRAPH_VERSION', 'v19.0'),

    /*
    |--------------------------------------------------------------------------
    | Meta App Configuration
    |--------------------------------------------------------------------------
    |
    | Your Meta App ID and Secret. These are used for generating App Secret
    | Proofs (if enabled) and for application-level authentication.
    |
    */
    'app' => [
        'id' => env('META_APP_ID'),
        'secret' => env('META_APP_SECRET'),
        'proof_enabled' => env('META_APPSECRET_PROOF_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | The token used to verify webhook subscriptions from Meta.
    |
    */
    'webhook' => [
        'verify_token' => env('META_WEBHOOK_VERIFY_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Persistence
    |--------------------------------------------------------------------------
    |
    | If disabled, the package operates in Direct API Mode. No migrations
    | will be published, and Eloquent models will not be used.
    |
    */
    'database' => [
        'enabled' => env('META_DATABASE_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduler
    |--------------------------------------------------------------------------
    |
    | If disabled, the package will not run queued jobs or scheduled tasks.
    |
    */
    'scheduler' => [
        'enabled' => env('META_SCHEDULER_ENABLED', true),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the underlying HTTP client making requests to Meta.
    |
    */
    'http' => [
        'timeout' => env('META_TIMEOUT', 30),
        'retry' => [
            'times' => 3,
            'sleep' => 100, // milliseconds
        ],
    ],
];
