<?php

namespace Tests\Feature\Authentication;

use Illuminate\Support\Str;
use App\User;
use Tests\TestCase;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_does_not_send_verification_emails_to_unauthorized_users()
    {
        Notification::fake();

        $client = factory(Client::class)->state('password')->create();

        $response = $this->getJson(route('verification.resend'), []);

        $response->assertStatus(401);
        Notification::assertNothingSent();
    }

    public function test_it_resends_verification_emails()
    {
        Notification::fake();

        $user = $this->actingAs(factory(User::class)->create());
        $this->assertNull($user->email_verification_code);

        $response = $this->getJson(route('verification.resend'), []);

        $response->assertStatus(200);
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->assertNotNull($user->fresh()->email_verification_code);
    }

    public function test_it_verifies_emails()
    {
        $user = factory(User::class)->states('unverified')->create([
            'email_verification_code' => Str::random(24)
        ]);
        $this->withHeaders($this->authorization($user));

        $response = $this->getJson(route('verification.verify', $user->email_verification_code));

        $response->assertStatus(200);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNull($user->fresh()->email_verification_code);
    }

    public function test_it_handles_invalid_verification_codes()
    {
        $user = factory(User::class)->states('unverified')->create([
            'email_verification_code' => Str::random(24)
        ]);
        $this->withHeaders($this->authorization($user));

        $response = $this->getJson(route('verification.verify', 'bad-code'));

        $response->assertStatus(422);
        $this->assertNull($user->fresh()->email_verified_at);
        $this->assertNotNull($user->fresh()->email_verification_code);
    }

    public function test_it_does_not_verify_unknown_accounts()
    {
        $client = factory(Client::class)->state('password')->create();

        $response = $this->getJson(route('verification.verify', 'FAKECODE'));

        $response->assertStatus(401);
    }
}
