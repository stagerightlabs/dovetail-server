<?php

namespace Tests\Feature;

use App\User;
use App\Category;
use Tests\TestCase;
use App\Organization;
use App\Events\CategoryDeletion;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_list_of_available_categories()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $categories = factory(Category::class, 3)->create([
            'organization_id' => $organization->id
        ]);

        $response = $this->getJson(route('categories.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => hashid($categories->first()->id),
            'name' => $categories->first()->name
        ]);
    }

    public function test_it_stores_new_categories()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->postJson(route('categories.store'), [
            'name' => 'Polymerase'
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'Polymerase'
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Polymerase'
        ]);
    }

    public function test_a_name_is_required()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->postJson(route('categories.store'), []);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('categories', ['name' => 'Polymerase']);
    }

    public function test_it_does_not_store_duplicate_categories_for_the_same_organization()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $category = factory(Category::class)->create([
            'name' => 'Polymerase',
            'organization_id' => $organization->id
        ]);

        $response = $this->postJson(route('categories.store'), [
            'name' => 'polymerase'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
        $this->assertEquals(1, Category::where('name', 'Polymerase')->count());
    }

    public function test_separate_organizations_can_have_the_same_category()
    {
        $organization = factory(Organization::class)->create();
        $category = factory(Category::class)->create([
            'name' => 'Polymerase',
            'organization_id' => $organization->id
        ]);
        $this->actingAs(factory(User::class)->create());

        $response = $this->postJson(route('categories.store'), [
            'name' => 'polymerase'
        ]);

        $response->assertStatus(201);
        $this->assertEquals(2, Category::count());
    }

    public function test_it_returns_a_single_category()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $category = factory(Category::class)->create([
            'name' => 'Polymerase',
            'organization_id' => $organization->id
        ]);

        $response = $this->getJson(route('categories.show', $category->hashid));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'hashid' => $category->hashid,
                'name' => $category->name,
            ]
        ]);
    }

    public function test_it_does_not_return_categories_that_do_not_exist()
    {
        $this->actingAs(factory(User::class)->create());

        $response = $this->getJson(route('categories.show', 'NOTREAL'));

        $response->assertStatus(404);
    }

    public function test_it_updates_categories()
    {
        $this->withoutExceptionHandling();
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $category = factory(Category::class)->create([
            'name' => 'Polymerase',
            'organization_id' => $organization->id
        ]);

        $response = $this->putJson(route('categories.update', $category->hashid), [
            'name' => 'DNA Polymerase'
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'hashid' => $category->hashid,
                'name' => 'DNA Polymerase'
            ]
        ]);
    }

    public function test_it_checks_uniqueness_when_updating_categories()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $category = factory(Category::class)->create([
            'name' => 'Polymerase',
            'organization_id' => $organization->id
        ]);
        factory(Category::class)->create([
            'name' => 'Enzymes',
            'organization_id' => $organization->id
        ]);

        $response = $this->putJson(route('categories.update', $category->hashid), [
            'name' => 'Enzymes'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
        $this->assertEquals(1, Category::where('name', 'Enzymes')->count());
    }

    public function test_it_deletes_categories()
    {
        Event::fake();
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $category = factory(Category::class)->create([
            'name' => 'Polymerase',
            'organization_id' => $organization->id
        ]);

        $response = $this->deleteJson(route('categories.destroy', $category->hashid));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', [
            'name' => 'Polymerase',
            'organization_id' => $organization->id
        ]);
        Event::assertDispatched(CategoryDeletion::class);
    }
}
