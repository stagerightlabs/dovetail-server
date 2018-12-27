<?php

namespace Tests\Unit;

use App\Team;
use App\User;
use App\Notebook;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotebookTest extends TestCase
{
    use RefreshDatabase;

    public function test_notebooks_can_be_assigned_to_users()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id,
        ]);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'created_by' => $user->id,
            'user_id' => $user->id
        ]);

        $this->assertTrue($user->notebooks->first()->is($notebook));
    }

    public function test_notebooks_can_be_assigned_to_teams()
    {
        $organization = factory(Organization::class)->create();
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id,
        ]);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'team_id' => $team->id
        ]);

        $this->assertTrue($team->notebooks->first()->is($notebook));
    }
}
