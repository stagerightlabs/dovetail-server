<?php

namespace App\Http\Controllers;

use App\Notebook;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\NotebookResource;
use App\Http\Controllers\Controller;

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
        request()->validate([
            'name' => 'required'
        ]);

        $notebook = Notebook::create([
            'name' => request('name'),
            'organization_id' => request()->organization()->id,
            'created_by' => auth()->user()->id,
            'category_id' => request()->filled('category_id') ? hashid(request('category_id')) : null
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
        return new NotebookResource(
            request()->organization()->notebooks()->findOrFail(hashid($hashid))
        );
    }

    /**
     * Update a notebook
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function update($hashid)
    {
        $notebook = request()->organization()->notebooks()->findOrFail(hashid($hashid));

        request()->validate([
            'name' => 'required'
        ]);

        $notebook->name = request('name');
        $notebook->category_id = request()->filled('category_id')
            ? hashid(request('category_id'))
            : $notebook->category_id;
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
        $notebook = request()->organization()->notebooks()->findOrFail(hashid($hashid));

        $notebook->delete();

        return response()->json([], 204);
    }
}
