<?php

namespace Tests\Feature;

use App\Logo;
use App\User;
use Tests\TestCase;
use App\Organization;
use App\Events\LogoCreated;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_organization_without_a_logo_is_assigned_a_null_object()
    {
        $organization = factory(Organization::class)->create();

        $this->assertInstanceOf(Logo::class, $organization->logo);
    }

    public function test_a_user_without_an_avatar_is_assigned_a_null_object()
    {
        $user = factory(User::class)->create();

        $this->assertInstanceOf(Logo::class, $user->avatar);
    }

    public function test_it_stores_a_logo_for_an_organization()
    {
        $organization = factory(Organization::class)->create();
        $file = File::image('logo.png', 850, 1100);
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        Storage::fake('s3');
        Event::fake();

        $response = $this->postJson(route('logos.store'), [
            'logo' => $file,
            'owner_type' => 'organization',
            'owner_hashid' => $organization->hashid
        ]);

        $response->assertStatus(201);
        $logo = Logo::first();
        $this->assertTrue($organization->logo->is($logo));
        $this->assertNotEmpty($logo->original);
        Storage::disk('s3')->assertExists($logo->original);
        Event::assertDispatched(LogoCreated::class);
    }

    public function test_it_stores_a_logo_for_a_user()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $file = File::image('logo.png', 850, 1100);
        Storage::fake('s3');
        Event::fake();

        $response = $this->postJson(route('logos.store'), [
            'logo' => $file,
            'owner_type' => 'user',
            'owner_hashid' => $user->hashid
        ]);

        $response->assertStatus(201);
        $logo = Logo::first();
        $this->assertTrue($user->avatar->is($logo));
        $this->assertNotEmpty($logo->original);
        Storage::disk('s3')->assertExists($logo->original);
        Event::assertDispatched(LogoCreated::class);
    }

    public function test_an_uploaded_file_is_required()
    {
        Storage::fake('s3');
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->postJson(route('logos.store'), [
            'owner_type' => 'organization',
            'owner_hashid' => $organization->hashid
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('logo');
        $this->assertEquals(0, Logo::count());
    }

    public function test_an_unqualified_owner_types_are_rejected()
    {
        Storage::fake('s3');
        $organization = factory(Organization::class)->create();
        $file = File::image('logo.png', 850, 1100);
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->postJson(route('logos.store'), [
            'logo' => $file,
            'owner_type' => 'invitation',
            'owner_hashid' => $organization->hashid
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('owner_type');
        $this->assertEquals(0, Logo::count());
    }

    public function test_only_image_files_may_be_used_for_logos()
    {
        Storage::fake('s3');
        $organization = factory(Organization::class)->create();
        $file = File::image('logo.pdf', 850, 1100);
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->postJson(route('logos.store'), [
            'logo' => $file,
            'owner_type' => 'organization',
            'owner_hashid' => $organization->hashid
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('logo');
        $this->assertEquals(0, Logo::count());
    }

    public function test_it_removes_logos()
    {
        $organization = factory(Organization::class)->create();
        $file = File::image('logo.png', 600, 800);
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        Storage::fake('s3');
        $logo = factory(Logo::class)->create([
            'owner_type' => 'organization',
            'owner_id' => $organization->id,
            'original' => $file->store('logos', 's3')
        ]);

        $response = $this->deleteJson(route('logos.delete', $logo->hashid));

        $response->assertStatus(204);
        Storage::disk('s3')->assertMissing($logo->original);
        $this->assertDatabaseMissing('logos', [
            'owner_type' => 'organization',
            'owner_id' => $organization->id,
        ]);
    }
}
