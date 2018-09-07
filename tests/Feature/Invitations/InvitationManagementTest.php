<?php

namespace Tests\Feature\Invitations;

use App\User;
use App\Invitation;
use Tests\TestCase;
use App\Notifications\InvitationSent;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_invitations()
    {
        $user = $this->actingAs(factory(User::class)->create());
        $invitationA = factory(Invitation::class)->create([
            'organization_id' => $user->organization_id,
            'email' => 'grace@example.com'
        ]);
        $invitationB = factory(Invitation::class)->create([
            'organization_id' => $user->organization_id,
            'email' => 'hopper@example.com'
        ]);

        $response = $this->getJson(route('invitations.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'organization_id' => $user->organization_id,
            'email' => 'grace@example.com',
            'revoked_at' => null,
            'revoked_by' => null,
            'completed_at' => null
        ]);
        $this->assertCount(2, $response->decodeResponseJson('data'));
    }

    public function test_it_does_not_show_invitations_to_non_org_users()
    {
        $userA = factory(User::class)->create();
        $userB = $this->actingAs(factory(User::class)->create());
        $invitationA = factory(Invitation::class)->create([
            'organization_id' => $userA->organization_id,
            'email' => 'grace@example.com'
        ]);
        $invitationB = factory(Invitation::class)->create([
            'organization_id' => $userA->organization_id,
            'email' => 'hopper@example.com'
        ]);

        $response = $this->getJson(route('invitations.index'));

        $response->assertStatus(200);
        $this->assertCount(0, $response->decodeResponseJson('data'));
    }

    public function test_it_creates_new_invitations()
    {
        Notification::fake();
        $user = $this->actingAs(factory(User::class)->create());

        $response = $this->postJson(route('invitations.store'), [
            'email' => 'grace@example.com'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('invitations', [
            'organization_id' => $user->organization_id,
            'email' => 'grace@example.com'
        ]);
        Notification::assertSentTo(
            new AnonymousNotifiable,
            InvitationSent::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] == 'grace@example.com';
            }
        );
    }

    public function test_only_org_admins_can_create_invitations()
    {
        Notification::fake();
        $user = $this->actingAs(factory(User::class)->states('org-user')->create());

        $response = $this->postJson(route('invitations.store'), [
            'email' => 'grace@example.com'
        ]);

        $response->assertStatus(403);
        Notification::assertNothingSent();
    }

    public function test_invitations_can_be_resent()
    {
        Notification::fake();
        $user = $this->actingAs(factory(User::class)->create());
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->postJson(route('invitations.resend', $invitation->hashid));

        $response->assertStatus(200);
        Notification::assertSentTo(
            new AnonymousNotifiable,
            InvitationSent::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] == 'grace@example.com';
            }
        );
    }

    public function test_only_org_admins_can_resend_invitations()
    {
        Notification::fake();
        $user = $this->actingAs(factory(User::class)->states('org-user')->create());
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->postJson(route('invitations.resend', $invitation->hashid));

        $response->assertStatus(403);
        Notification::assertNothingSent();
    }

    public function test_non_existant_invitations_cannot_be_resent()
    {
        Notification::fake();
        $user = $this->actingAs(factory(User::class)->create());

        $response = $this->postJson(route('invitations.resend', 'fakeid'));

        $response->assertStatus(404);
        Notification::assertNothingSent();
    }

    public function test_invitations_can_be_revoked()
    {
        $user = $this->actingAs(factory(User::class)->create());
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->postJson(route('invitations.revoke', $invitation->hashid));

        $response->assertStatus(200);
        $this->assertNotNull($invitation->fresh()->revoked_at);
        $this->assertEquals($invitation->fresh()->revoked_by, $user->id);
    }

    public function test_only_org_admins_can_revoke_invitations()
    {
        $user = $this->actingAs(factory(User::class)->states('org-user')->create());
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->postJson(route('invitations.revoke', $invitation->hashid));

        $response->assertStatus(403);
        $this->assertNull($invitation->fresh()->revoked_at);
        $this->assertNotEquals($invitation->fresh()->revoked_by, $user->id);
    }

    public function test_invitations_can_be_deleted()
    {
        $user = $this->actingAs(factory(User::class)->create());
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->deleteJson(route('invitations.destroy', $invitation->hashid));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('invitations', ['id' => $invitation->id]);
    }

    public function test_only_org_admins_can_destroy_invitations()
    {
        $user = $this->actingAs(factory(User::class)->states('org-user')->create());
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->deleteJson(route('invitations.destroy', $invitation->hashid));

        $response->assertStatus(403);
        $this->assertDatabaseHas('invitations', ['id' => $invitation->id]);
    }
}
