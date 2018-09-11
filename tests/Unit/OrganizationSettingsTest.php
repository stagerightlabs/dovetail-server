<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizations_have_default_settings()
    {
        $organization = factory(Organization::class)->create();

        $this->assertEquals($organization->settings->toArray(), Organization::$defaultConfiguration);
    }

    public function test_unknown_settings_return_null()
    {
        $organization = factory(Organization::class)->create();

        $this->assertNull($organization->config('unknown.setting'));
    }

    public function test_an_organization_can_have_configuration_changed()
    {
        $organization = factory(Organization::class)->create();

        $this->assertNull($organization->config('foo.bar'));

        $organization->updateConfiguration('foo.bar', 'baz');

        $this->assertEquals('baz', $organization->config('foo.bar'));
    }

    public function test_organization_configuration_can_be_set_in_bulk()
    {
        $organization = factory(Organization::class)->create();

        $this->assertNull($organization->config('foo'));
        $this->assertNull($organization->config('bar'));

        $organization->updateConfiguration([
            'foo' => 'baz',
            'bar' => 'buzz',
        ]);

        $this->assertEquals('baz', $organization->config('foo'));
        $this->assertEquals('buzz', $organization->config('bar'));
    }

    public function test_permissions_are_persisted()
    {
        $organization = factory(Organization::class)->create();

        $organization->updateConfiguration('foo.bar', 'baz');
        $organization->save();

        $this->assertEquals('baz', $organization->fresh()->config('foo.bar'));
    }

    public function test_permissions_can_be_revoked()
    {
        $organization = factory(Organization::class)->create();

        $organization->updateConfiguration('foo.bar', 'baz');
        $organization->save();

        $organization = $organization->fresh();

        $this->assertEquals('baz', $organization->config('foo.bar'));

        $organization->updateConfiguration('foo.bar', 'buzz');
        $organization->save();

        $this->assertEquals('buzz', $organization->fresh()->config('foo.bar'));
    }
}
