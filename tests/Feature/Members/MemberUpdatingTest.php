<?php

namespace Tests\Feature\Members;

use App\User;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MemberUpdatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_org_members_cannot_update_other_members()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);

        $response = $this->postJson(route('members.update', $member->hashid), [
            'name' => 'Admiral Hopper',
            'email' => 'hopper@example.com',
            'phone' => '(987) 654-3210'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => $member->name,
            'email' => $member->email,
            'phone' => $member->phone
        ]);
    }

    public function test_admins_from_other_organizations_cannot_update_members()
    {
        $organizationA = factory(Organization::class)->create();
        $organizationB = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organizationA->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organizationB->id,
            'email' => 'grace@example.com'
        ]);

        $response = $this->postJson(route('members.update', $member->hashid), [
            'name' => 'Admiral Hopper',
            'email' => 'hopper@example.com',
            'phone' => '(987) 654-3210'
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => $member->name,
            'email' => $member->email,
            'phone' => $member->phone
        ]);
    }

    public function test_admins_can_update_member_profiles()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);

        $response = $this->postJson(route('members.update', $member->hashid), [
            'name' => 'Admiral Hopper',
            'email' => 'hopper@example.com',
            'phone' => '(987) 654-3210',
            'title' => 'Admiral'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => 'Admiral Hopper',
            'email' => 'hopper@example.com',
            'phone' => '(987) 654-3210',
            'title' => 'Admiral'
        ]);
    }
}
