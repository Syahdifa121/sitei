<?php
// config/google-calendar.php

return [
    'api_key' => env('GOOGLE_CALENDAR_API_KEY'),
    'client_id' => env('GOOGLE_CALENDAR_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CALENDAR_CLIENT_SECRET'),
    'redirect_uri' => env('GOOGLE_CALENDAR_REDIRECT_URI'),
    
    'calendar_id' => env('GOOGLE_CALENDAR_ID'),
    
    'auth_config' => [
        'web' => [
            'client_id' => env('GOOGLE_CALENDAR_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CALENDAR_CLIENT_SECRET'),
            'redirect_uris' => [env('GOOGLE_CALENDAR_REDIRECT_URI')],
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
        ],
    ],

    'scopes' => [
        'https://www.googleapis.com/auth/calendar',
        'https://www.googleapis.com/auth/calendar.events',
    ],
];