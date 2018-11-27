<?php

namespace Tests\Feature\Invitations;

use App\User;
use App\Invitation;
use Tests\TestCase;
use App\Organization;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedemptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_valid_invitation_is_required()
    {
        $response = $this->postJson(route('invitations.redeem', 'FAKECODE'), [
            'name' => 'Grace',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('users', ['email' => 'grace@example.com']);
    }

    public function test_revoked_invitations_are_invalid()
    {
        $invitation = factory(Invitation::class)->states('revoked')->create([
            'email' => 'grace@example.com'
        ]);

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(404);
    }

    public function test_completed_invitations_are_invalid()
    {
        $invitation = factory(Invitation::class)->states('completed')->create([
            'email' => 'grace@example.com'
        ]);

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(404);
    }

    public function test_a_name_is_required()
    {
        $invitation = factory(Invitation::class)->create([
            'email' => 'grace@example.com'
        ]);

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_a_password_is_required()
    {
        $invitation = factory(Invitation::class)->create([
            'email' => 'grace@example.com'
        ]);

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_a_password_must_be_confirmed()
    {
        $invitation = factory(Invitation::class)->create([
            'email' => 'grace@example.com'
        ]);

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
            'password' => 'secret',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_an_invitation_can_be_redeemed()
    {
        $client = factory(Client::class)->state('password')->create();
        $organization = factory(Organization::class)->create();
        $invitation = factory(Invitation::class)->create([
            'email' => 'grace@example.com',
            'organization_id' => $organization->id
        ]);

        $response = $this->postJson(route('invitations.redeem', $invitation->code), [
            'name' => 'Grace',
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
        $this->assertNotNull($invitation->fresh()->completed_at);
        $user = User::where('email', 'grace@example.com')
            ->where('organization_id', $organization->id)
            ->first();
        $this->assertNotNull($user->email_verified_at);
    }
}
