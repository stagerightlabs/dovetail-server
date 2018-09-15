<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_have_default_permissions()
    {
        $member = factory(User::class)->states('org-member')->create();

        $this->assertEquals($member->permissions->toArray(), User::$defaultPermissions);
    }

    public function test_new_admins_are_assigned_default_admin_permissions()
    {
        $member = factory(User::class)->states('org-admin')->create();

        $this->assertEquals($member->permissions->toArray(), User::$defaultAdminPermissions);
    }

    public function test_unknown_permissions_return_false()
    {
        $member = factory(User::class)->create();

        $this->assertFalse($member->isAllowedTo('unknown.permission'));
    }

    public function test_a_user_can_be_granted_a_permission()
    {
        $member = factory(User::class)->create();

        $this->assertFalse($member->isAllowedTo('foo.bar'));

        $member->applyPermissions('foo.bar', true);

        $this->assertTrue($member->isAllowedTo('foo.bar'));
    }

    public function test_a_user_can_be_granted_bulk_permissions()
    {
        $member = factory(User::class)->create();

        $this->assertFalse($member->isAllowedTo('foo'));
        $this->assertFalse($member->isAllowedTo('bar'));

        $member->applyPermissions([
            'foo' => true,
            'bar' => true,
        ]);

        $this->assertTrue($member->isAllowedTo('foo'));
        $this->assertTrue($member->isAllowedTo('bar'));
    }

    public function test_permissions_are_persisted()
    {
        $member = factory(User::class)->create();

        $member->applyPermissions('foo.bar', true);
        $member->save();

        $this->assertTrue($member->fresh()->isAllowedTo('foo.bar'));
    }

    public function test_permissions_can_be_revoked()
    {
        $member = factory(User::class)->create();

        $member->applyPermissions('foo.bar', true);
        $member->save();

        $member = $member->fresh();

        $this->assertTrue($member->isAllowedTo('foo.bar'));

        $member->applyPermissions('foo.bar', false);
        $member->save();

        // dd($member->getOriginal('permission_flags'));

        $this->assertFalse($member->fresh()->isAllowedTo('foo.bar'));
    }
}
