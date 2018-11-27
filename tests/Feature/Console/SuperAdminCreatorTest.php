<?php

namespace Tests\Feature\Console;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuperAdminCreatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_super_admins()
    {
        Notification::fake();

        $this->artisan('auth:superadmin', [
            'email' => 'grace@example.com'
        ])->assertExitCode(0);

        $this->assertDatabaseHas('users', ['email' => 'grace@example.com']);
        $this->assertDatabaseHas('organizations', ['name' => 'Super Admins']);
    }

    public function test_it_validates_email_addresses()
    {
        Notification::fake();

        $this->artisan('auth:superadmin', [
            'email' => 'graceexamplecom'
        ])->assertExitCode(1);

        $this->assertDatabaseMissing('users', ['email' => 'grace@example.com']);
        $this->assertDatabaseMissing('organizations', ['name' => 'Super Admins']);

        Notification::assertNothingSent();
    }
}
