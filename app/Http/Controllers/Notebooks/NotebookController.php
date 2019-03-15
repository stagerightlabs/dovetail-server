<?php

namespace App\Http\Controllers\Notebooks;

use App\Notebook;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotebookUpdate;
use App\Http\Requests\NotebookCreation;
use App\Http\Resources\NotebookResource;

class NotebookController extends Controller
{
    /**
     * Fetch a list of available notebooks
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return NotebookResource::collection($request->organization()->notebooks);
    }

    /**
     * Store a new notebook
     *
     * @param  NotebookCreation $request
     * @return JsonResponse
     */
    public function store(NotebookCreation $request)
    {
        $notebook = Notebook::create([
            'name' => request('name'),
            'organization_id' => $request->organization()->id,
            'team_id' => hashid(request('team_id')),
            'user_id' => hashid(request('user_id')),
            'created_by' => $request->user()->id,
            'category_id' => $request->filled('category_id') ? hashid(request('category_id')) : null,
            'comments_enabled' => true,
        ]);

        return new NotebookResource($notebook);
    }

    /**
     * Return a single notebook
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function show($hashid)
    {
        $notebook = request()
            ->organization()
            ->notebooks()
            ->with([
                'pages',
                'pages.comments',
                'pages.activities',
                'pages.activities.causer',
                'pages.documents',
            ])->findOrFail(hashid($hashid));

        return new NotebookResource($notebook);
    }

    /**
     * Update a notebook
     *
     * @param  NotebookUpdate $request
     * @param  string $hashid
     * @return JsonResponse
     */
    public function update(NotebookUpdate $request, $hashid)
    {
        $notebook = $request->organization()->notebooks()->findOrFail(hashid($hashid));
        $notebook->name = request('name');
        $notebook->team_id = hashid($request->get('team_id'));
        $notebook->user_id = hashid($request->get('user_id'));
        $notebook->category_id = $request->has('category_id')
            ? hashid($request->get('category_id'))
            : $notebook->category_id;
        $notebook->comments_enabled = $request->has('comments_enabled')
            ? boolval($request->get('comments_enabled'))
            : $notebook->comments_enabled;
        $notebook->save();

        return new NotebookResource(
            $notebook->load(['pages', 'pages.comments', 'pages.activities', 'pages.activities.causer'])
        );
    }

    /**
     * Remove a notebook from storage.
     *
     * @param  Request $request
     * @param string $hashid
     * @return JsonResponse
     */
    public function destroy(Request $request, $hashid)
    {
        $this->requirePermission('notebooks.destroy');

        $notebook = $request->organization()->notebooks()->findOrFail(hashid($hashid));

        $notebook->delete();

        return response()->json([], 204);
    }
}
