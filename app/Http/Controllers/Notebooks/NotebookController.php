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
    public function index()
    {
        return NotebookResource::collection(request()->organization()->notebooks);
    }

    /**
     * Store a new notebook
     *
     * @return JsonResponse
     */
    public function store()
    {
        $this->requirePermission('notebooks.create');

        request()->validate([
            'name' => 'required'
        ]);

        $notebook = Notebook::create([
            'name' => request('name'),
            'organization_id' => request()->organization()->id,
            'team_id' => hashid(request('team_id')),
            'user_id' => hashid(request('user_id')),
            'created_by' => auth()->user()->id,
            'category_id' => request()->filled('category_id') ? hashid(request('category_id')) : null,
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
            ->with(['pages', 'pages.comments', 'pages.activities', 'pages.activities.causer'])
            ->findOrFail(hashid($hashid));

        return new NotebookResource($notebook);
    }

    /**
     * Update a notebook
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function update($hashid)
    {
        $this->requirePermission('notebooks.update');

        $notebook = request()->organization()->notebooks()->findOrFail(hashid($hashid));

        request()->validate([
            'name' => 'required',
            'comments_enabled' => 'nullable|boolean'
        ]);

        $notebook->name = request('name');
        $notebook->team_id = hashid(request('team_id'));
        $notebook->user_id = hashid(request('user_id'));
        $notebook->category_id = request()->filled('category_id')
            ? hashid(request('category_id'))
            : $notebook->category_id;
        $notebook->comments_enabled = request()->filled('comments_enabled')
            ? boolval(request('comments_enabled'))
            : $notebook->comments_enabled;
        $notebook->save();

        return new NotebookResource($notebook);
    }

    /**
     * Remove a notebook from storage.
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function delete($hashid)
    {
        $this->requirePermission('notebooks.delete');

        $notebook = request()->organization()->notebooks()->findOrFail(hashid($hashid));

        $notebook->delete();

        return response()->json([], 204);
    }
}
