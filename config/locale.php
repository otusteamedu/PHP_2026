<?php

return [

    'supported' => ['en', 'ru'],

    'default' => env('APP_LOCALE', 'ru'),

    'fallback' => env('APP_FALLBACK_LOCALE', 'en'),

    'testing' => env('LOCALE_TESTING', 'en'),

];
