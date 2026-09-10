<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'price_pro_monthly' => env('STRIPE_PRICE_PRO_MONTHLY'),
        'price_pro_yearly' => env('STRIPE_PRICE_PRO_YEARLY'),
        'price_lifetime' => env('STRIPE_PRICE_LIFETIME'),
    ],

    'github' => [
        // Used by App\Jobs\CrawlRepoSteeringDocs for the AI Steering Docs
        // crawler. Without this, every GitHub API call it makes is
        // unauthenticated (60 requests/hour) - nowhere near enough against
        // the ~500 target repos x up to 6 folders each it checks. A
        // classic (or fine-grained, public-repo-read) PAT with no special
        // scopes is enough; it only reads public repo contents.
        'token' => env('GITHUB_TOKEN'),
    ],

];
