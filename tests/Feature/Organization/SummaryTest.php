<?php

namespace Tests\Feature\Organization;

use App\User;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_retrieves_a_users_organization_summary()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->getJson(route('organization'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $organization->name,
            'slug' => $organization->slug,
        ]);
    }
}
