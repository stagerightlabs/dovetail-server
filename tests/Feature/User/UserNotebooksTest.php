<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use App\Team;
use App\User;
use App\Notebook;
use Tests\TestCase;
use App\Organization;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserNotebooksTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_a_users_notebooks()
    {
        $this->withoutExceptionHandling();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id,
        ]);
        $teamA = factory(Team::class)->create([
            'organization_id' => $organization->id,
        ]);
        $teamB = factory(Team::class)->create([
            'organization_id' => $organization->id,
        ]);
        $teamA->addMember($user);
        $notebookA = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $notebookB = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id
        ]);
        $notebookC = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'team_id' => $teamA->id
        ]);
        $notebookD = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'team_id' => $teamB->id
        ]);
        $this->actingAs($user);

        $response = $this->getJson(route('user.notebooks'));

        $response->assertJsonFragment([
            'hashid' => $notebookA->hashid,
            'name' => $notebookA->name,
            // 'slug' => Str::slug($teams[0]->name),
        ]);
        $this->assertCount(3, $response->decodeResponseJson('data'));
        $hashids = collect($response->decodeResponseJson('data'))->pluck('hashid');
        $this->assertTrue($hashids->contains($notebookA->hashid));
        $this->assertTrue($hashids->contains($notebookB->hashid));
        $this->assertTrue($hashids->contains($notebookC->hashid));
        $this->assertFalse($hashids->contains($notebookD->hashid));
    }
}
