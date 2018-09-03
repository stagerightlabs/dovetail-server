<?php

namespace Tests\Feature\Authentication;

use App\User;
use Tests\TestCase;
use Laravel\Passport\Token;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    public function test_it_terminates_access_tokens()
    {
        $user = $this->actingAs(factory(User::class)->create());
        $this->assertEquals(1, Token::where('user_id', $user->id)->where('revoked', false)->count());
        $this->assertEquals(0, Token::where('user_id', $user->id)->where('revoked', true)->count());

        $response = $this->postJson(route('logout'));

        $response->assertStatus(200);
        $this->assertEquals(0, Token::where('user_id', $user->id)->where('revoked', false)->count());
        $this->assertEquals(1, Token::where('user_id', $user->id)->where('revoked', true)->count());
    }

    public function test_a_valid_token_is_required_to_logout()
    {
        $user = factory(User::class)->create();

        $response = $this->postJson(route('logout'));

        $response->assertStatus(401);
    }
}
