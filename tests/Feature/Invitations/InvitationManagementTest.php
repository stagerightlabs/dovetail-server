<?php

namespace Tests\Feature\Invitations;

use App\User;
use App\Invitation;
use Tests\TestCase;
use App\Organization;
use App\Notifications\InvitationSent;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_invitations()
    {
        $user = $this->actingAs(factory(User::class)->states('org-admin')->create());
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
            'email' => 'grace@example.com',
            'revoked_at' => null,
            'revoked_by' => null,
            'completed_at' => null
        ]);
        $this->assertCount(2, $response->decodeResponseJson('data'));
    }

    public function test_it_only_shows_invitations_to_org_admins()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));
        $invitationA = factory(Invitation::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);
        $invitationB = factory(Invitation::class)->create([
            'organization_id' => $organization->id,
            'email' => 'hopper@example.com'
        ]);

        $response = $this->getJson(route('invitations.index'));

        $response->assertStatus(403);
    }

    public function test_it_does_not_show_invitations_to_non_org_users()
    {
        $user = factory(User::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create());
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
        $this->assertCount(0, $response->decodeResponseJson('data'));
    }

    public function test_it_creates_new_invitations()
    {
        Notification::fake();
        $user = factory(User::class)->states('org-admin')->create();
        $this->actingAs($user);

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

    public function test_it_does_not_create_duplicate_invitations()
    {
        Notification::fake();
        $this->actingAs(factory(User::class)->states('org-admin')->create());
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->postJson(route('invitations.store'), [
            'email' => 'grace@example.com'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $this->assertEquals(1, Invitation::where('email', 'grace@example.com')->count());
        Notification::assertNothingSent();
    }

    public function test_it_does_not_accept_invalid_email_addresses()
    {
        Notification::fake();
        $this->actingAs(factory(User::class)->states('org-admin')->create());

        $response = $this->postJson(route('invitations.store'), [
            'email' => 'grace@examplecom'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        Notification::assertNothingSent();
    }

    public function test_only_org_admins_can_create_invitations()
    {
        Notification::fake();
        $this->actingAs(factory(User::class)->states('org-member')->create());

        $response = $this->postJson(route('invitations.store'), [
            'email' => 'grace@example.com'
        ]);

        $response->assertStatus(403);
        Notification::assertNothingSent();
    }

    public function test_invitations_can_be_resent()
    {
        Notification::fake();
        $this->actingAs(factory(User::class)->states('org-admin')->create());
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
        $this->actingAs(factory(User::class)->states('org-member')->create());
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->postJson(route('invitations.resend', $invitation->hashid));

        $response->assertStatus(403);
        Notification::assertNothingSent();
    }

    public function test_non_existent_invitations_cannot_be_resent()
    {
        Notification::fake();
        $this->actingAs(factory(User::class)->states('org-admin')->create());

        $response = $this->postJson(route('invitations.resend', 'fakeid'));

        $response->assertStatus(404);
        Notification::assertNothingSent();
    }

    public function test_invitations_can_be_revoked()
    {
        $user = factory(User::class)->states('org-admin')->create();
        $this->actingAs($user);
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->postJson(route('invitations.revoke', $invitation->hashid));

        $response->assertStatus(200);
        $this->assertNotNull($invitation->fresh()->revoked_at);
        $this->assertEquals($invitation->fresh()->revoked_by, $user->id);
    }

    public function test_only_org_admins_can_revoke_invitations()
    {
        $user = factory(User::class)->states('org-member')->create();
        $this->actingAs($user);
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->postJson(route('invitations.revoke', $invitation->hashid));

        $response->assertStatus(403);
        $this->assertNull($invitation->fresh()->revoked_at);
        $this->assertNotEquals($invitation->fresh()->revoked_by, $user->id);
    }

    public function test_an_invitation_can_be_restored()
    {
        $user = factory(User::class)->states('org-admin')->create();
        $this->actingAs($user);
        $invitation = factory(Invitation::class)->states('revoked')
            ->create(['revoked_by' => $user->id]);

        $response = $this->deleteJson(route('invitations.restore', $invitation->hashid));

        $response->assertStatus(200);
        $this->assertNull($invitation->fresh()->revoked_at);
        $this->assertNull($invitation->fresh()->revoked_by);
    }

    public function test_only_org_admins_can_restore_invitations()
    {
        $this->actingAs(factory(User::class)->states('org-member')->create());
        $invitation = factory(Invitation::class)->states('revoked')->create();

        $response = $this->deleteJson(route('invitations.restore', $invitation->hashid));

        $response->assertStatus(403);
        $this->assertNotNull($invitation->fresh()->revoked_at);
        $this->assertNotNull($invitation->fresh()->revoked_by);
    }

    public function test_invitations_can_be_deleted()
    {
        $this->actingAs(factory(User::class)->states('org-admin')->create());
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->deleteJson(route('invitations.destroy', $invitation->hashid));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('invitations', ['id' => $invitation->id]);
    }

    public function test_only_org_admins_can_destroy_invitations()
    {
        $this->actingAs(factory(User::class)->states('org-member')->create());
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);

        $response = $this->deleteJson(route('invitations.destroy', $invitation->hashid));

        $response->assertStatus(403);
        $this->assertDatabaseHas('invitations', ['id' => $invitation->id]);
    }

    public function test_deleted_invitations_can_be_recreated()
    {
        Notification::fake();
        $user = factory(User::class)->states('org-admin')->create();
        $this->actingAs($user);
        $invitation = factory(Invitation::class)->create(['email' => 'grace@example.com']);
        $invitation->delete();

        $response = $this->postJson(route('invitations.store'), [
            'email' => 'grace@example.com'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('invitations', [
            'organization_id' => $user->organization_id,
            'email' => 'grace@example.com',
        ]);
        $this->assertEquals(1, Invitation::where('email', 'grace@example.com')->count());
        Notification::assertSentTo(
            new AnonymousNotifiable,
            InvitationSent::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] == 'grace@example.com';
            }
        );
    }

    public function test_an_invitation_cannot_be_created_afresh_once_revoked()
    {
        $user = factory(User::class)->states('org-admin')->create();
        $this->actingAs($user);
        $invitation = factory(Invitation::class)->states('revoked')
            ->create([
                'revoked_by' => $user->id,
                'email' => 'grace@example.com'
             ]);

        $response = $this->postJson(route('invitations.store'), [
            'email' => 'grace@example.com'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $this->assertEquals(1, Invitation::where('email', 'grace@example.com')->count());
    }

    public function test_invitations_cannot_be_sent_to_existing_users()
    {
        $this->actingAs(factory(User::class)->states('org-admin')->create());
        $user = factory(User::class)->create(['email' => 'grace@example.com']);

        $response = $this->postJson(route('invitations.store'), [
            'email' => 'grace@example.com'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $this->assertEquals(0, Invitation::where('email', 'grace@example.com')->count());
    }
}
