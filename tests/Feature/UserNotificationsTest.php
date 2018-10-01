<?php

namespace Tests\Feature;

use App\Page;
use App\User;
use App\Comment;
use App\Notebook;
use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserNotificationsTest extends TestCase
{
    public function test_a_user_can_see_their_unread_notifications()
    {
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $notebook->addFollower(collect([$memberA, $memberB]));
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $comment = factory(Comment::class)->create([
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'commentator_id' => $memberA->id,
        ]);
        $this->actingAs($memberB);

        $response = $this->getJson(route('user.notifications.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'uuid',
                    'type',
                    'data',
                    'read_at',
                    'created_at'
                ]
            ]
        ]);
    }

    public function test_a_user_can_view_a_single_notification()
    {
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $notebook->addFollower(collect([$memberA, $memberB]));
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $comment = factory(Comment::class)->create([
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'commentator_id' => $memberA->id,
        ]);
        $notification = $memberB->notifications()->first();
        $this->actingAs($memberB);

        $response = $this->getJson(route('user.notifications.show', $notification->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'uuid' => $notification->id,
            'type' => 'App\Notifications\CommentCreated',
            'read_at' => null,
            'created_at' => $notification->created_at->toAtomString()
        ]);
    }

    public function test_a_user_can_mark_a_notification_read()
    {
        $organization = factory(Organization::class)->create();
        $memberA = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $memberB = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $notebook->addFollower(collect([$memberA, $memberB]));
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $comment = factory(Comment::class)->create([
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'commentator_id' => $memberA->id,
        ]);
        $notification = $memberB->notifications()->first();
        $this->actingAs($memberB);

        $response = $this->putJson(route('user.notifications.update', $notification->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'uuid' => $notification->id,
            'type' => 'App\Notifications\CommentCreated',
            'created_at' => $notification->created_at->toAtomString()
        ]);
        $this->assertNotNull($notification->fresh()->read_at);
    }
}
