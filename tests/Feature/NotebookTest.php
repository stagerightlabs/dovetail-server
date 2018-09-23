<?php

namespace Tests\Feature;

use App\User;
use App\Category;
use App\Notebook;
use Tests\TestCase;
use App\Organization;
use App\Events\NotebookDeletion;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotebookTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_all_notebooks()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $notebooks = factory(Notebook::class, 3)->create([
            'organization_id' => $organization->id
        ]);

        $response = $this->getJson(route('notebooks.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => hashid($notebooks->first()->id),
            'name' => $notebooks->first()->name
        ]);
    }

    public function test_it_stores_a_notebook()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->postJson(route('notebooks.store'), [
            'name' => 'A Test Notebook'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('notebooks', [
            'name' => 'A Test Notebook',
            'organization_id' => $organization->id
        ]);
    }

    public function test_it_stores_a_notebook_with_a_category()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $category = factory(Category::class)->create();

        $response = $this->postJson(route('notebooks.store'), [
            'name' => 'A Test Notebook',
            'category_id' => $category->hashid
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('notebooks', [
            'name' => 'A Test Notebook',
            'organization_id' => $organization->id,
            'category_id' => $category->id
        ]);
    }

    public function test_notebooks_cannot_be_created_without_a_name()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->postJson(route('notebooks.store'), [
            //
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_it_returns_a_single_notebook()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);

        $response = $this->getJson(route('notebooks.show', $notebook->hashid));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => hashid($notebook->first()->id),
            'name' => $notebook->first()->name
        ]);
    }

    public function test_it_does_not_return_notebooks_that_do_not_exist()
    {
        $this->actingAs(factory(User::class)->create());

        $response = $this->getJson(route('notebooks.show', 'NOTREAL'));

        $response->assertStatus(404);
    }

    public function test_it_updates_a_notebook()
    {
        $this->withoutExceptionHandling();
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $notebook = factory(Notebook::class)->create([
            'name' => 'This is a name',
            'organization_id' => $organization->id
        ]);
        $category = factory(Category::class)->create();

        $response = $this->putJson(route('notebooks.update', $notebook->hashid), [
            'name' => 'Another Name',
            'category_id' => $category->hashid,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $notebook->hashid,
            'name' => 'Another Name'
        ]);
        $this->assertDatabaseHas('notebooks', [
            'name' => 'Another Name',
            'category_id' => $category->id,
            'organization_id' => $organization->id
        ]);
    }

    public function test_it_deletes_a_notebook()
    {
        Event::fake();
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);

        $response = $this->deleteJson(route('notebooks.delete', $notebook->hashid));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('notebooks', [
            'organization_id' => $organization->id
        ]);
        Event::assertDispatched(NotebookDeletion::class);
    }
}
