<?php

namespace App\Http\Controllers\Notebooks;

use App\Page;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\CommentResource;
use App\Http\Requests\PageCommentUpdate;
use App\Http\Requests\PageCommentCreation;
use Illuminate\Auth\Access\AuthorizationException;

class PageCommentController extends Controller
{
    /**
     * Fetch a list of available comments
     *
     * @param  string $notebook
     * @param  string $page
     * @return JsonResponse
     */
    public function index($notebook, $page)
    {
        $page = Page::with('notebook')->findOrFail(hashid($page));

        $this->authorize('view', [Comment::class, $page]);

        return CommentResource::collection($page->comments);
    }

    /**
     * Store a new comment
     *
     * @param  PageCommentCreation $request
     * @param  string $notebook
     * @param  string $page
     * @return JsonResponse
     */
    public function store(PageCommentCreation $request, $notebook, $page)
    {
        $page = Page::with('notebook')->findOrFail(hashid($page));

        $this->authorize('create', [Comment::class, $page]);

        $comment = $page->comments()->create([
            'content' => sanitize($request->get('content')),
            'commentator_id' => $request->user()->id
        ]);

        // Log the comment creation
        activity()->on($page)->log("New comment (" . strtoupper($comment->hashid) . ")");

        return new CommentResource($comment);
    }

    /**
     * Return a single comment
     *
     * @param  string $notebook
     * @param  string $page
     * @param  string $comment
     * @return JsonResponse
     */
    public function show($notebook, $page, $comment)
    {
        $page = Page::with('notebook')->findOrFail(hashid($page));

        $this->authorize('view', [Comment::class, $page]);

        return new CommentResource(
            Comment::findOrFail(hashid($comment))
        );
    }

    /**
     * Update a comment
     *
     * @param  PageCommentUpdate $request
     * @param  string $notebook
     * @param  string $page
     * @param  string $comment
     * @return JsonResponse
     */
    public function update(PageCommentUpdate $request, $notebook, $page, $comment)
    {
        $page = Page::with('notebook')->findOrFail(hashid($page));
        $comment = Comment::with('commentable')->findOrFail(hashid($comment));

        $this->authorize('update', $comment);

        $comment->content = sanitize($request->get('content'));
        $comment->edited = true;
        $comment->save();

        // Log the comment update
        activity()->on($page)->log("Updated comment (" . strtoupper($comment->hashid) . ")");

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
    public function destroy($notebook, $page, $comment)
    {
        $comment = Comment::findOrFail(hashid($comment));

        $this->authorize('forceDelete', $comment);

        $comment->delete();

        return response()->json([], 204);
    }
}
