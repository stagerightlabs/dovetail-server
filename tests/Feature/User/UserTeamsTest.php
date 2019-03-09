<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use App\Team;
use App\User;
use Tests\TestCase;
use App\Organization;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTeamsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_a_users_teams()
    {
        $user = factory(User::class)->create();
        $teams = factory(Team::class, 3)->create();
        $teams[0]->addMember($user);
        $teams[1]->addMember($user);
        $this->actingAs($user);

        $response = $this->getJson(route('user.teams'));

        $response->assertJsonFragment([
            'hashid' => $teams[0]->hashid,
            'name' => $teams[0]->name,
            'slug' => Str::slug($teams[0]->name),
        ]);
        $this->assertCount(2, $response->decodeResponseJson('data'));
    }
}
