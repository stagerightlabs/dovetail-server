<?php

namespace Tests\Feature\Authentication;

use App\User;
use Tests\TestCase;
use Illuminate\Http\Response;
use App\Http\Middleware\AddOrganizationToRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TokenUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_current_user()
    {
        $user = $this->actingAs(factory(User::class)->create());

        $response = $this->getJson(route('user.show'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    public function test_does_not_return_users_for_invalid_tokens()
    {
        $user = factory(User::class)->create();

        $response = $this->getJson(route('user.show'));

        $response->assertStatus(401);
    }

    public function test_it_returns_the_current_organization()
    {
        $user = $this->actingAs(factory(User::class)->create());
        $organization = $user->organization;

        $response = $this->getJson(route('organization'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $organization->name,
            'slug' => $organization->slug
        ]);
    }

    public function test_users_without_organizations_are_rejected()
    {
        $user = $this->actingAs(factory(User::class)->create([
            'organization_id' => 0
        ]));
        $organization = $user->organization;

        $response = $this->getJson(route('organization'));

        $response->assertStatus(403);
    }
}
