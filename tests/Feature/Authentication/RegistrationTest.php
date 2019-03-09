<?php

namespace Tests\Feature\Authentication;

use App\User;
use Tests\TestCase;
use Laravel\Passport\Client;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    public function test_a_user_can_register()
    {
        Notification::fake();
        $this->withoutExceptionHandling();
        $client = factory(Client::class)->state('password')->create();

        $response = $this->postJson(route('register'), [
            'name' => 'Grace',
            'email' => 'grace@example.com',
            'organization' => 'Phylos Bioscience',
            'password' => 'secretive',
            'password_confirmation' => 'secretive'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token'
        ]);
        $this->assertDatabaseHas('users', ['email' => 'grace@example.com']);
        $this->assertDatabaseHas('organizations', ['name' => 'Phylos Bioscience']);
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

    public function test_registration_email_must_be_valid()
    {
        Notification::fake();

        $response = $this->postJson(route('register'), [
            'name' => 'Grace',
            'email' => 'graceexample.com',
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
