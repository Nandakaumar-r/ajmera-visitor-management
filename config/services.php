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
    'snipeit' => [
        'url' => env('SNIPEIT_URL'),
        'token' => env('SNIPEIT_API_KEY'),
    ],
    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'api_url' => env('GROQ_API_URL'),
    ],

    'essl' => [
        'url' => env('ESSL_API_URL'),
        'key' => env('ESSL_API_KEY'),
    ],

    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'tenant_id' => env('MICROSOFT_TENANT_ID'),
        'user_id' => env('MICROSOFT_USER_ID', 'me'),
        'access_token' => env('MICROSOFT_ACCESS_TOKEN'),
    ],

    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'company_id' => env('LINKEDIN_COMPANY_ID', 'fidelis-technology-services'),
        'person_id' => env('LINKEDIN_PERSON_ID', 'subrahmanya-b-a-8278832a'),
        'redirect_uri' => env('LINKEDIN_REDIRECT_URI'),
        'access_token' => env('LINKEDIN_ACCESS_TOKEN'),
    ],

    'newsapi' => [
        'key' => env('NEWSAPI_KEY'),
    ],

];
