<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Organization;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_a_users_profile()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->getJson(route('user.show'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'hashid' => $user->hashid,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at->toAtomString(),
                'phone' => $user->phone,
                'phone_verified_at' => $user->phone_verified_at->toAtomString(),
                'teams' => []
            ]
        ]);
    }

    public function test_it_updates_a_users_profile()
    {
        $user = factory(User::class)->create([
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'phone' => '(123) 456-7890'
        ]);
        $this->actingAs($user);

        $response = $this->putJson(route('user.show'), [
            'name' => 'Admiral Hopper',
            'email' => 'hopper@example.com',
            'phone' => '(987) 654-3210',
            'title' => 'Admiral',
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'hashid' => $user->hashid,
                'name' => 'Admiral Hopper',
                'email' => 'hopper@example.com',
                'email_verified_at' => null,
                'phone' => '(987) 654-3210',
                'phone_verified_at' => null,
                'teams' => []
            ]
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Admiral Hopper',
            'email' => 'hopper@example.com',
            'phone' => '(987) 654-3210',
            'title' => null,
        ]);
    }

    public function test_changing_email_address_requires_revalidation()
    {
        $user = factory(User::class)->create([
            'email' => 'grace@example.com',
            'email_verified_at' => Carbon::now()->subDays(2)
        ]);
        $this->actingAs($user);

        $response = $this->putJson(route('user.show'), [
            'name' => 'Admiral Hopper',
            'email' => 'hopper@example.com',
            'phone' => '(987) 654-3210'
        ]);

        $response->assertStatus(200);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_changing_phone_requires_revalidation()
    {
        $user = factory(User::class)->create([
            'phone' => '(123) 456-7890',
            'phone_verified_at' => Carbon::now()->subDays(2)
        ]);
        $this->actingAs($user);

        $response = $this->putJson(route('user.show'), [
            'name' => 'Admiral Hopper',
            'email' => 'hopper@example.com',
            'phone' => '(987) 654-3210'
        ]);

        $response->assertStatus(200);
        $this->assertNull($user->fresh()->phone_verified_at);
    }

    public function test_guest_users_cannot_edit_profiles()
    {
        $client = factory(Client::class)->state('password')->create();

        $response = $this->putJson(route('user.show'), [
            'name' => 'Admiral Hopper',
            'email' => 'hopper@example.com',
            'phone' => '(987) 654-3210'
        ]);

        $response->assertStatus(401);
    }

    public function test_it_fetches_the_users_organization()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);

        $response = $this->getJson(route('organization'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $organization->hashid,
            'name' => $organization->name,
            'slug' => $organization->slug,
            'settings' => $organization->settings->toArray()
        ]);
    }

    public function test_it_retrieves_admin_status_for_administrators()
    {
        $user = factory(User::class)->state('org-admin')->create([
            'email' => 'grace@example.com',
        ]);
        $this->actingAs($user);

        $response = $this->getJson(route('user.flags.admin'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'admin' => true
            ]
        ]);
    }

    public function test_it_retrieves_admin_status_for_non_administrators()
    {
        $user = factory(User::class)->state('org-member')->create([
            'email' => 'grace@example.com',
        ]);
        $this->actingAs($user);

        $response = $this->getJson(route('user.flags.admin'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'admin' => false
            ]
        ]);
    }

    public function test_it_retrieves_readonly_status_for_regular_users()
    {
        $user = factory(User::class)->state('org-member')->create([
            'email' => 'grace@example.com',
        ]);
        $this->actingAs($user);

        $response = $this->getJson(route('user.flags.readonly'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'readonly' => false
            ]
        ]);
    }

    public function test_it_retrieves_readonly_status_for_readonly_users()
    {
        $user = factory(User::class)->state('org-readonly')->create([
            'email' => 'grace@example.com',
        ]);
        $this->actingAs($user);

        $response = $this->getJson(route('user.flags.readonly'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'readonly' => true
            ]
        ]);
    }
}
