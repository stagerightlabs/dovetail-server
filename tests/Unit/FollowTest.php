<?php

namespace Tests\Unit;

use App\Team;
use App\User;
use App\Notebook;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_removing_team_members_updates_notebook_followers()
    {
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($memberA);
        $team->addMember($memberB);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'team_id' => $team->id
        ]);


        $this->assertEquals(2, $notebook->getFollowers()->count());
        $team->removeMember($memberB);
        $this->assertEquals(1, $notebook->getFollowers()->count());
    }

    public function test_adding_team_members_updates_notebook_followers()
    {
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($memberA);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'team_id' => $team->id
        ]);


        $this->assertEquals(1, $notebook->getFollowers()->count());
        $team->addMember($memberB);
        $this->assertEquals(2, $notebook->getFollowers()->count());
    }
}
