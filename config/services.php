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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

      'sipintu' => [
        'base_url'      => env('SIPINTU_BASE_URL', 'https://sipintu.smkn1bangsri.sch.id'),
        'client_id'     => env('SIPINTU_CLIENT_ID'),
        'client_secret' => env('SIPINTU_CLIENT_SECRET'),
        'timeout'       => env('SIPINTU_TIMEOUT', 60),
        'verify_ssl'    => env('SIPINTU_VERIFY_SSL', false),
    ],




];
