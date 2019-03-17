<?php

namespace App\Http\Controllers\Notebooks;

use App\Page;
use HTMLPurifier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;

class PageController extends Controller
{
    /**
     * Fetch a list of available pages
     *
     * @param  Request $request
     * @param  string $notebook - Notebook hashid
     * @return JsonResponse
     */
    public function index(Request $request, $notebook)
    {
        $notebook = $request->organization()->notebooks()->findOrFail(hashid($notebook));

        return PageResource::collection($notebook->pages);
    }

    /**
     * Store a new page
     *
     * @param  Request $request
     * @param  string $notebook - Notebook hashid
     * @return JsonResponse
     */
    public function store(Request $request, $notebook)
    {
        $this->requirePermission('notebooks.pages');

        $notebook = $request->organization()->notebooks()->findOrFail(hashid($notebook));

        $currentPageCount = $notebook->pages()->count();

        $page = Page::create([
            'notebook_id' => $notebook->id,
            'created_by' => $request->user()->id,
            'content' => app(HTMLPurifier::class)->purify($request->get('content', '')),
            'sort_order' => $currentPageCount
        ]);

        return new PageResource($page);
    }

    /**
     * Return a single page
     *
     * @param  Request $request
     * @param  string $notebook - Notebook hashid
     * @param  string $page - Page hashid
     * @return JsonResponse
     */
    public function show(Request $request, $notebook, $page)
    {
        $notebook = $request->organization()->notebooks()->findOrFail(hashid($notebook));

        return new PageResource(
            $notebook->pages()->findOrFail(hashid($page))
        );
    }

    /**
     * Update a page
     *
     * @param  Request $request
     * @param  string $notebook - Notebook hashid
     * @param  string $page - Page hashid
     * @return JsonResponse
     */
    public function update(Request $request, $notebook, $page)
    {
        $this->requirePermission('notebooks.pages');

        $notebook = $request->organization()->notebooks()->findOrFail(hashid($notebook));
        $page = $notebook->pages()->findOrFail(hashid($page));

        $page->content = app(HTMLPurifier::class)->purify($request->get('content', ''));
        $page->save();

        return new PageResource($page);
    }

    /**
     * Remove a page from storage.
     *
     * @param  Request $request
     * @param  string $notebook - Notebook hashid
     * @param  string $page - Page hashid
     * @return JsonResponse
     */
    public function destroy(Request $request, $notebook, $page)
    {
        $this->requirePermission('notebooks.pages');

        $notebook = $request->organization()->notebooks()->findOrFail(hashid($notebook));
        $page = $notebook->pages()->findOrFail(hashid($page));

        $page->delete();

        return response()->json([], 204);
    }
}
