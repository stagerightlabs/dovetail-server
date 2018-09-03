<?php

namespace Tests\Feature\Authentication;

use App\User;
use Tests\TestCase;
use Laravel\Passport\Token;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_login()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $client = factory(Client::class)->state('password')->create();

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'secret'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token'
        ]);
        $this->assertEquals(1, Token::where('user_id', $user->id)->count());
    }

    public function test_it_requires_correct_credentials()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->state('password')->create();

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'bad-password'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $this->assertEquals(0, Token::count());
    }

    public function test_it_requires_an_existing_email()
    {
        $client = factory(Client::class)->state('password')->create();

        $response = $this->postJson(route('login'), [
            'email' => 'notreal@example.com',
            'password' => 'bad-password'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $this->assertEquals(0, Token::count());
    }
}
