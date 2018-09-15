<?php

namespace Tests\Feature\Teams;

use App\Team;
use App\User;
use Tests\TestCase;
use App\Organization;
use App\Events\TeamDeletion;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_all_available_teams()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);

        $response = $this->getJson(route('teams.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $team->hashid,
            'name' => $team->name
        ]);
    }

    public function test_other_organizations_cannot_see_these_teams()
    {
        $organizationA = factory(Organization::class)->create();
        $organizationB = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organizationA->id
        ]));
        $team = factory(Team::class)->create([
            'organization_id' => $organizationB->id
        ]);

        $response = $this->getJson(route('teams.index'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => []
        ]);
    }

    public function test_teams_can_be_created_by_users_with_permission()
    {
        $organization = factory(Organization::class)->create();
        $admin = factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($admin);

        $this->assertTrue($admin->hasPermission('teams.create'));

        $response = $this->postJson(route('teams.store'), [
            'name' => 'Red Team'
        ]);

        $response->assertStatus(201);
    }

    public function test_users_cannot_be_created_by_users_without_permission()
    {
        $organization = factory(Organization::class)->create();
        $admin = factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($admin);

        $this->assertFalse($admin->hasPermission('teams.create'));

        $response = $this->postJson(route('teams.store'), [
            'name' => 'Red Team'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('teams', ['name' => 'Red Team']);
    }

    public function test_teams_must_have_names()
    {
        $organization = factory(Organization::class)->create();
        $admin = factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($admin);

        $this->assertTrue($admin->hasPermission('teams.create'));

        $response = $this->postJson(route('teams.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_it_does_not_create_duplicate_teams()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $team = factory(Team::class)->create([
            'name' => 'Red Team',
            'organization_id' => $organization->id
        ]);

        $response = $this->postJson(route('teams.store'), [
            'name' => 'red team'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
        $this->assertEquals(1, Team::count());
    }

    public function test_different_organizations_can_have_teams_with_the_same_name()
    {
        $organizationA = factory(Organization::class)->create();
        $organizationB = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organizationA->id
        ]));
        $team = factory(Team::class)->create([
            'name' => 'Red Team',
            'organization_id' => $organizationB->id
        ]);

        $response = $this->postJson(route('teams.store'), [
            'name' => 'Red Team'
        ]);

        $response->assertStatus(201);
        $this->assertEquals(2, Team::count());
    }

    public function test_it_returns_a_single_team()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);

        $response = $this->getJson(route('teams.show', $team->hashid));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $team->hashid,
            'name' => $team->name
        ]);
    }

    public function test_a_user_with_permission_can_update_a_team()
    {
        $organization = factory(Organization::class)->create();
        $admin = factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($admin);
        $this->assertTrue($admin->hasPermission('teams.update'));
        $team = factory(Team::class)->create([
            'name' => 'Red Team',
            'organization_id' => $organization->id
        ]);

        $response = $this->putJson(route('teams.update', $team->hashid), [
            'name' => 'Blue Team'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $team->hashid,
            'name' => 'Blue Team',
        ]);
        $this->assertDatabaseHas('teams', [
            'name' => 'Blue Team',
            'organization_id' => $organization->id
        ]);
    }

    public function test_a_user_without_permission_cannot_update_a_team()
    {
        $organization = factory(Organization::class)->create();
        $member = factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($member);
        $this->assertFalse($member->hasPermission('teams.update'));
        $team = factory(Team::class)->create([
            'name' => 'Red Team',
            'organization_id' => $organization->id
        ]);

        $response = $this->putJson(route('teams.update', $team->hashid), [
            'name' => 'Blue Team'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('teams', [
            'name' => 'Blue Team',
            'organization_id' => $organization->id
        ]);
    }

    public function test_a_user_with_permission_can_delete_a_team()
    {
        $this->withoutExceptionHandling();
        $organization = factory(Organization::class)->create();
        $admin = factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($admin);
        $this->assertTrue($admin->hasPermission('teams.delete'));
        $team = factory(Team::class)->create([
            'name' => 'Red Team',
            'organization_id' => $organization->id
        ]);
        Event::fake();

        $response = $this->deleteJson(route('teams.update', $team->hashid));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('teams', [
            'name' => 'Red Team',
            'organization_id' => $organization->id
        ]);
        Event::assertDispatched(TeamDeletion::class);
    }

    // public function test_teams_with_members_can_be_deleted()
    // {
    // }
}
