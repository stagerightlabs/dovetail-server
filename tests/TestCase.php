<?php

namespace Tests;

use App\User;
use Laravel\Passport\Client;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function authorization(Authenticatable $user)
    {
        $client = factory(Client::class)->state('password')->create();

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'secret'
        ]);

        if ($response->status() != 200) {
            $this->fail("An access token could not be generated for {$user->email}");
        }

        $token = 'Bearer ' . $response->decodeResponseJson('access_token');

        return ['Authorization' => $token];
    }

    public function actingAs(Authenticatable $user, $driver = null)
    {
        $this->withHeaders($this->authorization($user));

        return $user;
    }
}
