<?php

namespace Tests\Feature\Authentication;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_register()
    {
        Notification::fake();

        $response = $this->postJson(route('register'), [
            'name' => 'Grace',
            'email' => 'grace@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'grace@example.com']);
        Notification::assertSentTo(
            User::where('email', 'grace@example.com')->first(),
            VerifyEmail::class
        );
    }

    public function test_email_is_required()
    {
        Notification::fake();

        $response = $this->postJson(route('register'), [
            'name' => 'Grace',
            'email' => '',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'grace@example.com']);
        Notification::assertNothingSent();
    }

    public function test_password_is_required()
    {
        Notification::fake();

        $response = $this->postJson(route('register'), [
            'name' => 'Grace',
            'email' => 'grace@example.com',
            'password' => '',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'grace@example.com']);
        Notification::assertNothingSent();
    }

    public function test_password_must_be_confirmed()
    {
        Notification::fake();

        $response = $this->postJson(route('register'), [
            'name' => 'Grace',
            'email' => 'grace@example.com',
            'password' => 'secret',
            'password_confirmation' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'grace@example.com']);
        Notification::assertNothingSent();
    }

    public function test_name_is_required()
    {
        Notification::fake();

        $response = $this->postJson(route('register'), [
            'name' => '',
            'email' => 'grace@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
        $this->assertDatabaseMissing('users', ['email' => 'grace@example.com']);
        Notification::assertNothingSent();
    }
}
