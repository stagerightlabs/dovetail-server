<?php

namespace Tests\Feature\Members;

use App\User;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MemberViewingTest extends TestCase
{
    use RefreshDatabase;

    public function test_org_users_can_see_organization_members()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-user')->create([
            'organization_id' => $organization->id
        ]));
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'hopper@example.com'
        ]);

        $response = $this->getJson(route('members.get'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'email',
                    'name',
                    'rank',
                    'created_at'
                ]
            ]
        ]);
        $this->assertEquals(3, count($response->decodeResponseJson('data')));
    }

    public function test_org_admins_can_see_organization_members()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'hopper@example.com'
        ]);

        $response = $this->getJson(route('members.get'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'email',
                    'name',
                    'rank',
                    'phone',
                    'email_verified',
                    'phone_verified',
                    'permissions'
                ]
            ]
        ]);
        $this->assertEquals(3, count($response->decodeResponseJson('data')));
    }
}
