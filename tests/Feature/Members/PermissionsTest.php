<?php

namespace Tests\Feature\Members;

use App\User;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_view_a_members_permissions()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);

        $response = $this->getJson(route('permissions.show', $member->hashid));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => array_keys(User::$defaultPermissions)
        ]);
    }

    public function test_a_member_cannot_view_another_members_permissions()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);

        $response = $this->getJson(route('permissions.show', $member->hashid));

        $response->assertStatus(403);
    }

    public function test_an_outside_admin_cannot_view_permissions()
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

        $response = $this->getJson(route('permissions.show', $member->hashid));

        $response->assertStatus(404);
    }

    public function test_a_member_can_check_their_own_permissions()
    {
        $this->withoutExceptionHandling();
        $member = factory(User::class)->states('org-member')->create();
        $this->actingAs($member);

        $member->applyPermissions([
            'foo' => true,
            'bar' => false
        ]);
        $member->save();

        $response = $this->getJson(route('user.permission', 'foo'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'key' => 'foo',
                'allowed' => true
            ]
        ]);

        $response = $this->getJson(route('user.permission', 'bar'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'key' => 'bar',
                'allowed' => false
            ]
        ]);
    }

    public function test_an_admin_can_update_a_members_permissions()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);
        $this->assertFalse($member->isAllowedTo('notebooks.create'));

        $response = $this->postJson(route('permissions.update', $member->hashid), [
            'permissions' => ['notebooks.create' => true]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => array_keys(User::$defaultPermissions)
        ]);
        $this->assertTrue($member->fresh()->isAllowedTo('notebooks.create'));
    }

    public function test_an_admin_can_cannot_write_arbitrary_member_permissions()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);
        $this->assertFalse($member->isAllowedTo('foo'));

        $response = $this->postJson(route('permissions.update', $member->hashid), [
            'permissions' => ['foo' => true]
        ]);

        $response->assertStatus(200);
        $this->assertFalse($member->fresh()->isAllowedTo('foo'));
    }

    public function test_a_member_cannot_update_another_members_permissions()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);
        $this->assertFalse($member->isAllowedTo('foo'));

        $response = $this->postJson(route('permissions.update', $member->hashid), [
            'permissions' => ['foo' => true]
        ]);

        $response->assertStatus(403);
        $this->assertFalse($member->isAllowedTo('foo'));
    }

    public function test_a_member_cannot_update_their_own_permissions()
    {
        $organization = factory(Organization::class)->create();
        $member = factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);

        $this->actingAs($member);

        $this->assertFalse($member->isAllowedTo('foo'));

        $response = $this->postJson(route('permissions.update', $member->hashid), [
            'permissions' => ['foo' => true]
        ]);

        $response->assertStatus(403);
        $this->assertFalse($member->isAllowedTo('foo'));
    }
}
