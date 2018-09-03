<?php

namespace Tests\Feature\Authentication;

use App\User;
use Tests\TestCase;
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
}
