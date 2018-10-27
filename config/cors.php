<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */

    'supportsCredentials' => false,
    'allowedOrigins' => [env('FRONTEND_BASE_URL')],
    'allowedOriginsPatterns' => [],
    'allowedHeaders' => ['Authorization', 'Content-Type', 'X-Requested-With', 'Origin'],
    'allowedMethods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'exposedHeaders' => [],
    'maxAge' => 0,

];
