<?php

namespace Tests\Feature\Invitations;

use App\User;
use App\Invitation;
use Tests\TestCase;
use App\Organization;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedemptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_valid_invitation_is_required()
    {
        $response = $this->postJson(route('invitations.redeem', 'FAKECODE'), [
            'name' => 'Grace',
            'email' => 'grace@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('users', ['email' => 'grace@example.com']);
    }

    public function test_revoked_invitations_are_invalid()
    {
        $invitation = factory(Invitation::class)->states('revoked')->create();

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
            'email' => 'grace@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(404);
    }

    public function test_completed_invitations_are_invalid()
    {
        $invitation = factory(Invitation::class)->states('completed')->create();

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
            'email' => 'grace@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(404);
    }

    public function test_an_email_is_required()
    {
        $invitation = factory(Invitation::class)->create();

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_a_name_is_required()
    {
        $invitation = factory(Invitation::class)->create();

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'email' => 'grace@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_a_password_is_required()
    {
        $invitation = factory(Invitation::class)->create();

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
            'email' => 'grace@example.com',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_a_password_must_be_confirmed()
    {
        $invitation = factory(Invitation::class)->create();

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
            'email' => 'grace@example.com',
            'password' => 'secret',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_an_invitation_can_be_redeemed()
    {
        Notification::fake();
        $client = factory(Client::class)->state('password')->create();
        $organization = factory(Organization::class)->create();
        $invitation = factory(Invitation::class)->create([
            'organization_id' => $organization->id
        ]);

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
            'email' => 'grace@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token'
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'grace@example.com',
            'organization_id' => $organization->id
        ]);
        Notification::assertSentTo(
            User::where('email', 'grace@example.com')->first(),
            VerifyEmail::class
        );
    }
}
