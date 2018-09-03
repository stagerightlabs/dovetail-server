<?php

use Laravel\Passport\Client;

/**************************************************************************
 * OAuth Fixtures
 **************************************************************************/

$factory->define(Client::class, function () {
    return [
        'user_id' => null,
        'name' => 'OAuth Client',
        'secret' => 'l6s92mlRBkvOaucPW0kzcQWXAApnnFiYYLgDcxZ8',
        'redirect' => 'http://localhost',
        'personal_access_client' => false,
        'password_client' => false,
        'revoked' => false
    ];
});

$factory->state(Client::class, 'password', [
    'name' => 'Password Client',
    'password_client' => true,
]);
