<?php

namespace Tests\Feature\Authentication;

use App\User;
use Tests\TestCase;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resends_verification_emails()
    {
        Notification::fake();

        $client = factory(Client::class)->state('password')->create();

        $response = $this->getJson(route('verification.resend'), []);

        $response->assertStatus(401);
        Notification::assertNothingSent();
    }

    public function test_it_does_not_send_verification_emails_to_unauthorized_users()
    {
        Notification::fake();

        $user = $this->actingAs(factory(User::class)->create());

        $response = $this->getJson(route('verification.resend'), []);

        $response->assertStatus(200);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_it_verifies_emails()
    {
        $user = factory(User::class)->states('unverified')->create();
        $this->withHeaders($this->authorization($user));
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->getKey()]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(200);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_it_does_not_verify_unknown_accounts()
    {
        $client = factory(Client::class)->state('password')->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => 1]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(401);
    }
}
