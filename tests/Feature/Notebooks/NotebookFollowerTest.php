<?php

namespace Tests\Feature\Notebooks;

use App\Team;
use App\User;
use App\Follow;
use App\Notebook;
use Tests\TestCase;
use App\Organization;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotebookFollowerTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_follow_their_own_notebooks()
    {
        $organization = factory(Organization::class)->create();
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.create' => true]);
        $member->save();
        $this->actingAs($member);

        $response = $this->postJson(route('notebooks.store'), [
            'name' => 'A Test Notebook',
            'user_id' => $member->hashid
        ]);

        $notebook = Notebook::first();
        $this->assertDatabaseHas('follows', [
            'user_id' => $member->id,
            'followable_id' => $notebook->id,
            'followable_type' => 'notebook'
        ]);
    }

    public function test_teams_follow_their_own_notebooks()
    {
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberA->applyPermissions(['notebooks.create' => true]);
        $memberA->save();
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($memberA);
        $team->addMember($memberB);
        $this->actingAs($memberA);

        $response = $this->postJson(route('notebooks.store'), [
            'name' => 'A Test Notebook',
            'team_id' => $team->hashid
        ]);

        $notebook = Notebook::first();
        $this->assertDatabaseHas('follows', [
            'user_id' => $memberA->id,
            'followable_id' => $notebook->id,
            'followable_type' => 'notebook'
        ]);
        $this->assertDatabaseHas('follows', [
            'user_id' => $memberB->id,
            'followable_id' => $notebook->id,
            'followable_type' => 'notebook'
        ]);
    }

    public function test_org_members_do_not_automatically_follow_org_notebooks()
    {
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberA->applyPermissions(['notebooks.create' => true]);
        $memberA->save();
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($memberA);

        $response = $this->postJson(route('notebooks.store'), [
            'name' => 'A Test Notebook',
            'organization_id' => $organization->hashid
        ]);

        $notebook = Notebook::first();

        $this->assertEquals(0, $notebook->getFollowers()->count());
        $this->assertDatabaseMissing('follows', [
            'user_id' => $memberA->id,
            'followable_id' => $notebook->id,
            'followable_type' => 'notebook'
        ]);
        $this->assertDatabaseMissing('follows', [
            'user_id' => $memberB->id,
            'followable_id' => $notebook->id,
            'followable_type' => 'notebook'
        ]);
    }

    public function test_deleting_notebooks_removes_follow_records()
    {
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberA->applyPermissions(['notebooks.destroy' => true]);
        $memberA->save();
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($memberA);
        $team->addMember($memberB);
        $this->actingAs($memberA);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'team_id' => $team->id,
        ]);

        $this->assertEquals(2, $notebook->getFollowers()->count());

        $response = $this->deleteJson(route('notebooks.destroy', $notebook->hashid));

        $this->assertDatabaseMissing('follows', [
            'followable_type' => 'notebook',
            'followable_id' => $notebook->id,
        ]);
    }

    public function test_a_member_may_follow_a_notebook()
    {
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($memberB);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $memberA->id,
        ]);

        $response = $this->postJson(route('notebooks.follow', $notebook->hashid));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $notebook->name,
            'current_user_is_following' => true,
        ]);
        $this->assertDatabaseHas('follows', [
            'followable_type' => 'notebook',
            'followable_id' => $notebook->id,
            'user_id' => $memberB->id,
        ]);
    }

    public function test_a_member_may_unfollow_a_notebook()
    {
        $organization = factory(Organization::class)->create();
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($member);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $member->id,
        ]);

        $response = $this->deleteJson(route('notebooks.unfollow', $notebook->hashid));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $notebook->name,
            'current_user_is_following' => false
        ]);
        $this->assertDatabaseMissing('follows', [
            'followable_type' => 'notebook',
            'followable_id' => $notebook->id,
            'user_id' => $member->id,
        ]);
    }
}
