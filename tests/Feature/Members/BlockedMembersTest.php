<?php

namespace Tests\Feature\Members;

use App\User;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockedMembersTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_can_be_blocked()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
        ]);

        $response = $this->deleteJson(route('members.block', $member->hashid));

        $response->assertStatus(204);
        $this->assertNotNull($member->fresh()->blocked_at);
    }

    public function test_members_cannot_block_other_members()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organization->id,
        ]);

        $response = $this->deleteJson(route('members.block', $member->hashid));

        $response->assertStatus(403);
        $this->assertNull($member->fresh()->blocked_at);
    }

    public function test_users_from_other_orgs_cannot_block_members()
    {
        $organizationA = factory(Organization::class)->create();
        $organizationB = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organizationA->id
        ]));
        $member = factory(User::class)->create([
            'organization_id' => $organizationB->id,
        ]);

        $response = $this->deleteJson(route('members.block', $member->hashid));

        $response->assertStatus(404);
        $this->assertNull($member->fresh()->blocked_at);
    }

    public function test_blocked_members_can_be_unblocked()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->states('blocked')->create([
            'organization_id' => $organization->id,
        ]);

        $response = $this->deleteJson(route('members.unblock', $member->hashid));

        $response->assertStatus(204);
        $this->assertNull($member->fresh()->blocked_at);
    }

    public function test_members_cannot_restore_deleted_members()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));
        $member = factory(User::class)->states('blocked')->create([
            'organization_id' => $organization->id,
            'email' => 'grace@example.com'
        ]);

        $response = $this->deleteJson(route('members.unblock', $member->hashid));

        $response->assertStatus(403);
        $this->assertNotNull($member->fresh()->blocked_at);
    }
}
