<?php

namespace Tests\Feature\Notebooks;

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

    public function test_users_with_permission_can_create_a_notebook()
    {
        $organization = factory(Organization::class)->create();
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.create' => true]);
        $member->save();
        $this->actingAs($member);

        $response = $this->postJson(route('notebooks.store'), [
            'name' => 'A Test Notebook'
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'A Test Notebook',
            'comments_enabled' => true,
        ]);
        $this->assertDatabaseHas('notebooks', [
            'name' => 'A Test Notebook',
            'organization_id' => $organization->id,
            'comments_enabled' => true,
        ]);
    }

    public function test_users_without_permission_cannot_create_notebooks()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->postJson(route('notebooks.store'), [
            'name' => 'A Test Notebook'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('notebooks', [
            'name' => 'A Test Notebook',
            'organization_id' => $organization->id
        ]);
    }

    public function test_it_stores_a_notebook_with_a_category()
    {
        $organization = factory(Organization::class)->create();
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.create' => true]);
        $member->save();
        $this->actingAs($member);
        $category = factory(Category::class)->create();

        $response = $this->postJson(route('notebooks.store'), [
            'name' => 'A Test Notebook',
            'category_id' => $category->hashid
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'A Test Notebook',
            'category' => $category->name
        ]);
        $this->assertDatabaseHas('notebooks', [
            'name' => 'A Test Notebook',
            'organization_id' => $organization->id,
            'category_id' => $category->id
        ]);
    }

    public function test_notebooks_cannot_be_created_without_a_name()
    {
        $organization = factory(Organization::class)->create();
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.create' => true]);
        $member->save();
        $this->actingAs($member);

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
            'name' => $notebook->first()->name,
            'comments_enabled' => true,
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
        $organization = factory(Organization::class)->create();
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.update' => true]);
        $member->save();
        $this->actingAs($member);

        $notebook = factory(Notebook::class)->create([
            'name' => 'This is a name',
            'organization_id' => $organization->id,
            'comments_enabled' => true,
        ]);
        $category = factory(Category::class)->create();

        $response = $this->putJson(route('notebooks.update', $notebook->hashid), [
            'name' => 'Another Name',
            'category_id' => $category->hashid,
            'comments_enabled' => '0'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $notebook->hashid,
            'name' => 'Another Name',
            'comments_enabled' => false,
        ]);
        $this->assertDatabaseHas('notebooks', [
            'name' => 'Another Name',
            'category_id' => $category->id,
            'organization_id' => $organization->id,
            'comments_enabled' => false,
        ]);
    }

    public function test_users_with_permission_can_delete_notebooks()
    {
        Event::fake();
        $organization = factory(Organization::class)->create();
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.delete' => true]);
        $member->save();
        $this->actingAs($member);
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

    public function test_users_without_permission_cannot_delete_notebooks()
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

        $response->assertStatus(403);
        $this->assertDatabaseHas('notebooks', [
            'organization_id' => $organization->id
        ]);
        Event::assertNotDispatched(NotebookDeletion::class);
    }
}
