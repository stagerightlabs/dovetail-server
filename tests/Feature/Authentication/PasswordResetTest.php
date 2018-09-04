<?php

namespace Tests\Feature\Authentication;

use App\User;
use Tests\TestCase;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_reset_emails_to_valid_accounts()
    {
        Notification::fake();
        $user = factory(User::class)->create();

        $response = $this->postJson(route('password.email'), [
            'email' => $user->email
        ]);

        $response->assertStatus(200);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_it_does_not_send_reset_emails_to_invalid_accounts()
    {
        Notification::fake();
        $user = factory(User::class)->create();

        $response = $this->postJson(route('password.email'), [
            'email' => 'bademail@example.com'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        Notification::assertNothingSent();
    }

    public function test_users_with_active_tokens_cannot_request_reset_emails()
    {
        Notification::fake();
        $user = factory(User::class)->create();
        $this->withHeaders($this->authorization($user));

        $response = $this->postJson(route('password.email'), [
            'email' => $user->email
        ]);

        $response->assertStatus(405);
        Notification::assertNothingSent();
    }

    public function test_it_resets_passwords()
    {
        $user = factory(User::class)->create();
        $resetToken = Password::createToken($user);
        $client = factory(Client::class)->state('password')->create();

        $response = $this->postJson(route('password.update'), [
            'token' => $resetToken,
            'email' => $user->email,
            'password' => 'notsecret',
            'password_confirmation' => 'notsecret'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token'
        ]);
        $this->assertTrue(Hash::check('notsecret', $user->fresh()->password));
    }

    public function test_it_does_not_change_passwords_for_invalid_tokens()
    {
        $user = factory(User::class)->create();

        $response = $this->postJson(route('password.update'), [
            'token' => 'badtoken',
            'email' => $user->email,
            'password' => 'notsecret',
            'password_confirmation' => 'notsecret'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $this->assertFalse(Hash::check('notsecret', $user->fresh()->password));
    }

    public function test_it_does_not_change_passwords_for_unconfirmed_passwords()
    {
        $user = factory(User::class)->create();
        $resetToken = Password::createToken($user);

        $response = $this->postJson(route('password.update'), [
            'token' => $resetToken,
            'email' => $user->email,
            'password' => 'notsecret',
            'password_confirmation' => 'somethingelse'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
        $this->assertFalse(Hash::check('notsecret', $user->fresh()->password));
    }
}
