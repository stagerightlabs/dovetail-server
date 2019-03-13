<?php

namespace App\Http\Controllers\Notebooks;

use App\Notebook;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotebookResource;

class NotebookController extends Controller
{
    /**
     * Fetch a list of available notebooks
     *
     * @return JsonResponse
     */
    public function index(\Illuminate\Http\Request $request)
    {
        return NotebookResource::collection($request->organization()->notebooks);
    }

    /**
     * Store a new notebook
     *
     * @return JsonResponse
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $this->requirePermission('notebooks.create');

        $request->validate([
            'name' => 'required'
        ]);

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
     * @param string $hashid
     * @return JsonResponse
     */
    public function update(\Illuminate\Http\Request $request, $hashid)
    {
        $this->requirePermission('notebooks.update');

        $notebook = $request->organization()->notebooks()->findOrFail(hashid($hashid));

        $request->validate([
            'name' => 'required',
            'comments_enabled' => 'nullable|boolean'
        ]);

        $notebook->name = request('name');
        $notebook->team_id = hashid(request('team_id'));
        $notebook->user_id = hashid(request('user_id'));
        $notebook->category_id = $request->has('category_id')
            ? hashid(request('category_id'))
            : $notebook->category_id;
        $notebook->comments_enabled = $request->has('comments_enabled')
            ? boolval(request('comments_enabled'))
            : $notebook->comments_enabled;
        $notebook->save();

        return new NotebookResource(
            $notebook->load(['pages', 'pages.comments', 'pages.activities', 'pages.activities.causer'])
        );
    }

    /**
     * Remove a notebook from storage.
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function delete(\Illuminate\Http\Request $request, $hashid)
    {
        $this->requirePermission('notebooks.delete');

        $notebook = $request->organization()->notebooks()->findOrFail(hashid($hashid));

        $notebook->delete();

        return response()->json([], 204);
    }
}
