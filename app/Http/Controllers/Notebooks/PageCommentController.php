<?php

namespace App\Http\Controllers\Notebooks;

use App\Page;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\CommentResource;
use Illuminate\Auth\Access\AuthorizationException;

class PageCommentController extends Controller
{
    /**
     * Fetch a list of available comments
     *
     * @param string $notebook
     * @param string $page
     * @return JsonResponse
     */
    public function index($notebook, $page)
    {
        $page = Page::findOrFail(hashid($page));

        return CommentResource::collection($page->comments);
    }

    /**
     * Store a new comment
     *
     * @param string $notebook
     * @param string $page
     * @return JsonResponse
     */
    public function store($notebook, $page)
    {
        request()->validate([
            'content' => 'required'
        ]);

        $page = Page::findOrFail(hashid($page));

        $comment = $page->comments()->create([
            'content' => sanitize(request('content')),
            'commentor_id' => auth()->user()->id
        ]);

        return new CommentResource($comment);
    }

    /**
     * Return a single comment
     *
     * @param string $notebook
     * @param string $page
     * @param string $comment
     * @return JsonResponse
     */
    public function show($notebook, $page, $comment)
    {
        return new CommentResource(
            Comment::findOrFail(hashid($comment))
        );
    }

    /**
     * Update a comment
     *
     * @param string $notebook
     * @param string $page
     * @param string $comment
     * @return JsonResponse
     */
    public function update($notebook, $page, $comment)
    {
        $comment = Comment::findOrFail(hashid($comment));

        // Users can only edit their own comments
        Gate::authorize('ownership-verification', [$comment, 'commentor_id']);

        request()->validate([
            'content' => 'required'
        ]);

        $comment->content = sanitize(request('content'));
        $comment->edited = true;
        $comment->save();

        return new CommentResource($comment);
    }

    /**
     * Remove a comment from storage.
     *
     * @param string $notebook
     * @param string $page
     * @param string $comment
     * @return JsonResponse
     */
    public function delete($notebook, $page, $comment)
    {
        $comment = Comment::findOrFail(hashid($comment));

        $comment->delete();

        return response()->json([], 204);
    }
}
