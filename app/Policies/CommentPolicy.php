<?php

namespace App\Policies;

use App\Page;
use App\User;
use App\Model;
use App\Comment;
use App\AccessLevel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Whitelist all the policies in this class for users who qualify
     *
     * @param User $user
     * @param string $ability
     * @return void
     */
    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can create comments.
     *
     * @param  \App\User  $user
     * @param Model $commentable
     * @return mixed
     */
    public function create(User $user, $commentable)
    {
        // Are we commenting on a Notebook page?
        if ($commentable instanceof Page) {
            return $commentable->notebook->comments_enabled;
        }

        return false;
    }

    /**
     * Determine whether the user can update the comment.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function update(User $user, Comment $comment)
    {
        // Users can only edit their own comments
        if (Gate::denies('ownership-verification', [$comment, 'commentor_id'])) {
            return false;
        }

        // Are we commenting on a Notebook page?
        if ($comment->commentable instanceof Page) {
            return $comment->commentable->notebook->comments_enabled;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the comment.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function delete(User $user, Comment $comment)
    {
        // Organization Admins can delete comments
        if ($user->access_level >= AccessLevel::$ORGANIZATION_ADMIN) {
            return true;
        }

        // Are we commenting on a Notebook page? Check that commenting is enabled
        if ($comment->commentable instanceof Page
            && !$comment->commentable->notebook->comments_enabled) {
            return false;
        }

        // Users may only delete their own comments
        return Gate::allows('ownership-verification', [$comment, 'commentor_id']);
    }

    /**
     * Determine whether the user can permanently delete the comment.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function forceDelete(User $user, Comment $comment)
    {
        // Organization Admins can delete comments
        if ($user->access_level >= AccessLevel::$ORGANIZATION_ADMIN) {
            return true;
        }

        // Are we commenting on a Notebook page? Check that commenting is enabled
        if ($comment->commentable instanceof Page
            && !$comment->commentable->notebook->comments_enabled) {
            return false;
        }

        // Users may only delete their own comments
        return Gate::allows('ownership-verification', [$comment, 'commentor_id']);
    }
}
