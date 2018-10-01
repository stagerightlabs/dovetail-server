<?php

namespace Tests\Feature\Notebooks;

use App\Page;
use App\Team;
use App\User;
use App\Comment;
use App\Notebook;
use Tests\TestCase;
use App\Organization;
use App\Notifications\CommentCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_all_comments_for_a_page()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $comment = factory(Comment::class)->create([
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'commentator_id' => $user->id,
        ]);

        $response = $this->getJson(route('pages.comments.index', [$notebook->hashid, $page->hashid]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $comment->hashid,
            'content' => $comment->content,
            'commentator_id' => $user->hashid
        ]);
    }

    public function test_it_stores_a_comment_on_a_page()
    {
        Notification::fake();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);

        $response = $this->postJson(route('pages.comments.store', [$notebook->hashid, $page->hashid]), [
            'content' => "This is a comment",
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'content' => 'This is a comment',
            'commentator_id' => $user->hashid,
        ]);
        $this->assertDatabaseHas('comments', [
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'content' => 'This is a comment',
            'commentator_id' => $user->id
        ]);
        Notification::assertNothingSent();
    }

    public function test_it_sanitizes_comment_content()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);

        $response = $this->postJson(route('pages.comments.store', [$notebook->hashid, $page->hashid]), [
            'content' => "This is a comment<script>badstuff();</script>",
        ]);

        $this->assertDatabaseHas('comments', [
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'content' => 'This is a comment',
            'commentator_id' => $user->id
        ]);
    }

    public function test_it_returns_a_single_page_comment()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $comment = factory(Comment::class)->create([
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'commentator_id' => $user->id,
        ]);

        $response = $this->getJson(route('pages.comments.show', [$notebook->hashid, $page->hashid, $comment->hashid]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $comment->hashid,
            'content' => $comment->content,
            'commentator_id' => $user->hashid,
            'commentator' => $user->name,
        ]);
    }

    public function test_users_outside_of_an_organization_cannot_view_comments()
    {
        $organization = factory(Organization::class)->create();
        $userA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $userB = factory(User::class)->create();
        $this->actingAs($userB);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $comment = factory(Comment::class)->create([
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'commentator_id' => $userA->id,
        ]);

        $response = $this->getJson(route('pages.comments.show', [$notebook->hashid, $page->hashid, $comment->hashid]));

        $response->assertStatus(403);
    }

    public function test_it_does_not_return_page_comments_that_do_not_exist()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);

        $response = $this->getJson(route('pages.comments.show', [$notebook->hashid, $page->hashid, 'NOTREAL']));

        $response->assertStatus(404);
    }

    public function test_it_does_not_create_comments_on_notebooks_with_comments_disabled()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'comments_enabled' => false,
        ]);

        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);

        $response = $this->postJson(route('pages.comments.store', [$notebook->hashid, $page->hashid]), [
            'content' => "This is a comment",
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('comments', [
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'content' => 'This is a comment',
            'commentator_id' => $user->id
        ]);
    }

    public function test_it_updates_a_page_comment()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $comment = factory(Comment::class)->create([
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'commentator_id' => $user->id,
            'edited' => false
        ]);

        $response = $this->putJson(route('pages.comments.update', [$notebook->hashid, $page->hashid, $comment->hashid]), [
            'content' => 'This comment has changed'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $comment->hashid,
            'content' => 'This comment has changed',
            'edited' => true
        ]);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'This comment has changed',
            'edited' => true
        ]);
    }

    public function test_it_does_not_update_comments_on_notebooks_with_comments_disabled()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'comments_enabled' => false,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $comment = factory(Comment::class)->create([
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'commentator_id' => $user->id,
            'edited' => false
        ]);

        $response = $this->putJson(route('pages.comments.update', [$notebook->hashid, $page->hashid, $comment->hashid]), [
            'content' => 'This comment has changed'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
            'content' => 'This comment has changed',
            'edited' => true
        ]);
    }

    public function test_users_can_only_edit_their_own_comments()
    {
        $organization = factory(Organization::class)->create();
        $userA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $userB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($userB);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $comment = factory(Comment::class)->create([
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'commentator_id' => $userA->id,
            'edited' => false
        ]);

        $response = $this->putJson(route('pages.comments.update', [$notebook->hashid, $page->hashid, $comment->hashid]), [
            'content' => 'This comment has changed'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
            'content' => 'This comment has changed',
        ]);
    }

    public function test_it_deletes_a_page_comment()
    {
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $this->actingAs($user);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $comment = factory(Comment::class)->create([
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'commentator_id' => $user->id,
            'edited' => false
        ]);

        $response = $this->deleteJson(route('pages.comments.delete', [$notebook->hashid, $page->hashid, $comment->hashid]));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id
        ]);
    }

    public function test_notebook_followers_are_notified_when_comments_are_created()
    {
        Notification::fake();
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberA->applyPermissions(['notebooks.create' => true]);
        $memberA->save();
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($memberA);
        $team->addMember($memberB);
        $this->actingAs($memberA);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'team_id' => $team->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);

        $response = $this->postJson(route('pages.comments.store', [$notebook->hashid, $page->hashid]), [
            'content' => "This is a comment",
        ]);

        Notification::assertSentTo($memberB, CommentCreated::class);
        Notification::assertNotSentTo($memberA, CommentCreated::class);
    }

    public function test_users_who_unfollow_notebooks_are_not_notified_about_new_comments()
    {
        Notification::fake();
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberA->applyPermissions(['notebooks.create' => true]);
        $memberA->save();
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($memberA);
        $team->addMember($memberB);
        $this->actingAs($memberA);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'team_id' => $team->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);

        $notebook->removeFollower($memberB);

        $response = $this->postJson(route('pages.comments.store', [$notebook->hashid, $page->hashid]), [
            'content' => "This is a comment",
        ]);

        Notification::assertNotSentTo($memberB, CommentCreated::class);
        Notification::assertNotSentTo($memberA, CommentCreated::class);
    }

    public function test_deleted_followers_are_not_notified_about_new_comments()
    {
        Notification::fake();
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberA->applyPermissions(['notebooks.create' => true]);
        $memberA->save();
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $team = factory(Team::class)->create([
            'organization_id' => $organization->id
        ]);
        $team->addMember($memberA);
        $team->addMember($memberB);
        $this->actingAs($memberA);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
            'team_id' => $team->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);

        $memberB->delete();

        $response = $this->postJson(route('pages.comments.store', [$notebook->hashid, $page->hashid]), [
            'content' => "This is a comment",
        ]);

        Notification::assertNotSentTo($memberB, CommentCreated::class);
        Notification::assertNotSentTo($memberA, CommentCreated::class);
    }
}
