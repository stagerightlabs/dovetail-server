<?php

namespace Tests\Feature\Members;

use App\User;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeletedMembersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_only_returns_deleted_users()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);
        $memberB = factory(User::class)->states('deleted')->create([
            'organization_id' => $organization->id,
            'email' => 'hopper@example.com'
        ]);

        $response = $this->getJson(route('members.deleted'));

        $this->assertEquals(1, count($response->decodeResponseJson('data')));
    }

    public function test_members_cannot_see_deleted_users()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->getJson(route('members.deleted'));

        $response->assertStatus(403);
    }

    public function test_members_can_be_deleted()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
        ]);

        $response = $this->deleteJson(route('members.delete', $member->hashid));

        $response->assertStatus(204);
        $this->assertSoftDeleted('users', ['id' => $member->id]);
    }

    public function test_members_cannot_delete_other_members()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
        ]);

        $response = $this->deleteJson(route('members.delete', $member->hashid));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $member->id, 'deleted_at' => null]);
    }

    public function test_users_from_other_orgs_cannot_delete_members()
    {
        $organizationA = factory(Organization::class)->create();
        $organizationB = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organizationA->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organizationB->id,
        ]);

        $response = $this->deleteJson(route('members.delete', $member->hashid));

        $response->assertStatus(404);
        $this->assertDatabaseHas('users', ['id' => $member->id, 'deleted_at' => null]);
    }

    public function test_deleted_members_can_be_restored()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->states('deleted')->create([
            'organization_id' => $organization->id,
        ]);

        $response = $this->deleteJson(route('members.restore', $member->hashid));

        $response->assertStatus(204);
        $this->assertDatabaseHas('users', ['id' => $member->id, 'deleted_at' => null]);
    }

    public function test_members_cannot_restore_deleted_members()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->states('deleted')->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);

        $response = $this->deleteJson(route('members.delete', $member->hashid));

        $response->assertStatus(403);
        $this->assertSoftDeleted('users', ['id' => $member->id]);
    }
}
