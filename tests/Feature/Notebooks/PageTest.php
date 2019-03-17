<?php

namespace Tests\Feature\Notebooks;

use App\Page;
use App\User;
use App\Notebook;
use Tests\TestCase;
use App\Organization;
use App\Events\PageDeletion;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_all_pages()
    {
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $pages = factory(Page::class, 3)->create([
            'notebook_id' => $notebook->id
        ]);

        $response = $this->getJson(route('pages.index', $notebook->hashid));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => hashid($pages->first()->id),
            'content' => $pages->first()->content,
            'notebook_id' => $notebook->hashid,
        ]);
    }

    public function test_users_with_permission_can_create_notebook_pages()
    {
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.pages' => true]);
        $member->save();
        $this->actingAs($member);

        $response = $this->postJson(route('pages.store', $notebook->hashid), [
            'content' => 'Lorem Ipsum Text'
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'content' => 'Lorem Ipsum Text',
            'notebook_id' => $notebook->hashid,
        ]);
        $this->assertDatabaseHas('pages', [
            'notebook_id' => $notebook->id,
            'created_by' => $member->id,
            'content' => 'Lorem Ipsum Text',
        ]);
    }

    public function test_a_user_without_permission_cannot_create_pages()
    {
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.pages' => false]);
        $member->save();
        $this->actingAs($member);
        $this->assertFalse($member->hasPermission('notebooks.pages'));

        $response = $this->postJson(route('pages.store', $notebook->hashid), [
            'content' => 'Lorem Ipsum Text'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('pages', [
            'notebook_id' => $notebook->id
        ]);
    }

    public function test_it_returns_a_single_page()
    {
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id
        ]);

        $response = $this->getJson(route('pages.show', [$notebook->hashid, $page->hashid]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'content' => $page->content,
            'notebook_id' => $notebook->hashid,
        ]);
    }

    public function test_it_does_not_return_pages_that_do_not_exist()
    {
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));

        $response = $this->getJson(route('pages.show', [$notebook->hashid, 'NOTREAL']));

        $response->assertStatus(404);
    }

    public function test_users_with_permission_can_update_pages()
    {
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.pages' => true]);
        $member->save();
        $this->actingAs($member);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id
        ]);

        $response = $this->putJson(route('pages.update', [$notebook->hashid, $page->hashid]), [
            'content' => 'Different Content'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'content' => 'Different Content',
            'notebook_id' => $notebook->hashid,
        ]);
        $this->assertDatabaseHas('pages', [
            'notebook_id' => $notebook->id,
            'content' => 'Different Content'
        ]);
    }

    public function test_users_without_permission_cannot_update_pages()
    {
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.pages' => false]);
        $member->save();
        $this->actingAs($member);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id
        ]);

        $response = $this->putJson(route('pages.update', [$notebook->hashid, $page->hashid]), [
            'content' => 'Different Content'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('pages', [
            'notebook_id' => $notebook->id,
            'content' => $page->content,
        ]);
    }

    public function test_users_with_permission_can_delete_pages()
    {
        Event::fake();
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.pages' => true]);
        $member->save();
        $this->actingAs($member);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id
        ]);

        $response = $this->deleteJson(route('pages.destroy', [$notebook->hashid, $page->hashid]));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('pages', [
            'id' => $page->id
        ]);
        Event::assertDispatched(PageDeletion::class);
    }

    public function test_users_without_permission_cannot_delete_pages()
    {
        Event::fake();
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.pages' => false]);
        $member->save();
        $this->actingAs($member);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id
        ]);

        $response = $this->deleteJson(route('pages.destroy', [$notebook->hashid, $page->hashid]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('pages', [
            'id' => $page->id
        ]);
        Event::assertNotDispatched(PageDeletion::class);
    }

    public function test_page_content_is_sanitized()
    {
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.pages' => true]);
        $member->save();
        $this->actingAs($member);

        $response = $this->postJson(route('pages.store', $notebook->hashid), [
            'notebook_id' => $notebook->id,
            'content' => 'Lorem Ipsum Text<script>badstuff();</script>'
        ]);

        $this->assertDatabaseHas('pages', [
            'notebook_id' => $notebook->id,
            'created_by' => $member->id,
            'content' => 'Lorem Ipsum Text',
        ]);
    }

    public function test_notebook_page_sort_order_can_be_updated()
    {
        $organization = factory(Organization::class)->create();
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id
        ]);
        $member = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $member->applyPermissions(['notebooks.pages' => true]);
        $member->save();
        $this->actingAs($member);
        $pages = factory(Page::class, 3)->create([
            'notebook_id' => $notebook->id
        ]);

        $firstPage = $pages->shift();
        $secondPage = $pages->shift();
        $thirdPage = $pages->shift();

        $response = $this->putJson(route('notebooks.sort-order', $notebook->hashid), [
            'pages' => [$thirdPage->hashid, $firstPage->hashid, $secondPage->hashid]
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseHas('pages', ['id' => $firstPage->id, 'sort_order' => 1]);
        $this->assertDatabaseHas('pages', ['id' => $secondPage->id, 'sort_order' => 2]);
        $this->assertDatabaseHas('pages', ['id' => $thirdPage->id, 'sort_order' => 0]);
    }
}
