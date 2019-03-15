<?php

namespace Tests\Feature\Console;

use App\User;
use Tests\TestCase;
use App\Organization;
use App\Billing\PaymentGateway;
use Illuminate\Support\Facades\DB;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationCreationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    public function test_it_creates_organizations()
    {
        Notification::fake();

        $this->artisan('auth:register')
            ->expectsQuestion('User email address?', 'grace@example.com')
            ->expectsQuestion('User profile name?', 'Grace Hopper')
            ->expectsQuestion('Organization name?', 'Hopper Labs')
            ->expectsQuestion('Create this new account?', 'y')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', ['email' => 'grace@example.com']);
        $this->assertDatabaseHas('organizations', ['name' => 'Hopper Labs']);

        Notification::assertSentTo(
            User::first(),
            \App\Notifications\VerifyEmail::class
        );
    }

    public function test_it_does_not_create_duplicate_users()
    {
        Notification::fake();
        factory(User::class)->create(['email' => 'grace@example.com']);

        $this->artisan('auth:register')
            ->expectsQuestion('User email address?', 'grace@example.com')
            ->assertExitCode(1);

        $this->assertEquals(1, DB::table('users')->where('email', 'grace@example.com')->count());
        Notification::assertNothingSent();
    }

    public function test_it_does_not_create_duplicate_organizations()
    {
        Notification::fake();
        factory(Organization::class)->create(['name' => 'Hopper Labs']);

        $this->artisan('auth:register')
            ->expectsQuestion('User email address?', 'grace@example.com')
            ->expectsQuestion('User profile name?', 'Grace Hopper')
            ->expectsQuestion('Organization name?', 'Hopper Labs')
            ->assertExitCode(1);

        $this->assertEquals(0, DB::table('users')->where('email', 'grace@example.com')->count());
        $this->assertEquals(1, DB::table('organizations')->where('name', 'Hopper Labs')->count());
        Notification::assertNothingSent();
    }
}
