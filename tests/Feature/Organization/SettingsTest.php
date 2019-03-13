<?php

namespace Tests\Feature\Organization;

use App\User;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_organization_member_can_read_settings()
    {
        $organization = factory(Organization::class)->create();
        $organization->updateConfiguration('label.plates', 'something else');
        $organization->save();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->getJson(route('settings.show', 'label.plates'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'key' => 'label.plates',
                'value' => 'something else'
            ]
        ]);
    }

    public function test_an_organization_member_cannot_write_settings()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-member')->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->putJson(route('settings.update'), [
            'settings' => ['foo' => 'bar']
        ]);

        $response->assertStatus(403);
        $this->assertNull($organization->fresh()->config('foo'));
    }

    public function test_an_organization_admin_can_write_settings()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->putJson(route('settings.update'), [
            'key' => 'label.plates',
            'value' => 'bar'
        ]);

        $response->assertStatus(204);
        $this->assertEquals('bar', $organization->fresh()->config('label.plates'));
    }

    public function test_it_does_not_accept_arbitrary_values()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->states('org-admin')->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->putJson(route('settings.update'), [
            'key' => 'arbitrary',
            'value' => 'foo',
        ]);

        $response->assertStatus(204);
        $this->assertNull($organization->fresh()->config('arbitrary'));
    }
}
