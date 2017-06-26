<?php

return [
    'middlewareGroups' => [
        'web' => [
            'Illuminate\Cookie\Middleware\EncryptCookies',
            'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
            'Illuminate\Session\Middleware\StartSession',
        ],
    ],

    'routeMiddleware' => [
    ],
];
