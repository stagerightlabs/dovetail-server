<?php

namespace Tests\Feature\Teams;

use App\Team;
use App\User;
use Tests\TestCase;
use App\Organization;
use App\Events\TeamMemberAdded;
use App\Events\TeamMemberRemoved;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualified_users_can_add_members_to_teams()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($user);
        $this->assertTrue($user->hasPermission('teams.membership'));
        Event::fake();

        $response = $this->postJson(route('teams.memberships.store', $team->hashid), [
            'member' => $member->hashid
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $member->id
        ]);
        $this->assertTrue($team->hasMember($member));
        Event::assertDispatched(TeamMemberAdded::class);
    }

    public function test_non_qualified_users_cannot_add_members_to_teams()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);

        $this->assertFalse($user->hasPermission('teams.membership'));

        $response = $this->postJson(route('teams.memberships.store', $team->hashid), [
            'member' => $member->hashid
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $member->id
        ]);
        $this->assertFalse($team->hasMember($member));
    }

    public function test_users_cannot_add_themselves_to_teams()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);

        $this->assertTrue($user->hasPermission('teams.membership'));

        $response = $this->postJson(route('teams.memberships.store', $team->hashid), [
            'member' => $user->hashid
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id
        ]);
        $this->assertFalse($team->hasMember($user));
    }

    public function test_qualified_users_can_remove_team_members()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($user);
        $this->assertTrue($user->hasPermission('teams.membership'));
        Event::fake();

        $response = $this->deleteJson(route('teams.memberships.delete', [$team->hashid, $member->hashid]));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $member->id
        ]);
        Event::assertDispatched(TeamMemberRemoved::class);
    }

    public function test_unqualified_cannot_remove_members_from_teams()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($member);

        $this->assertFalse($user->hasPermission('teams.membership'));

        $response = $this->deleteJson(route('teams.memberships.delete', [$team->hashid, $member->hashid]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $member->id
        ]);
        $this->assertTrue($team->hasMember($member));
    }

    public function test_users_cannot_remove_themselves_from_groups()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($user);

        $this->assertTrue($user->hasPermission('teams.membership'));

        $response = $this->deleteJson(route('teams.memberships.delete', [$team->hashid, $user->hashid]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
        $this->assertTrue($team->hasMember($user));
    }
}
