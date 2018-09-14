<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Fetch a list of available categories
     *
     * @return JsonResponse
     */
    public function index()
    {
        return CategoryResource::collection(request()->organization()->categories);
    }

    /**
     * Store a new category
     *
     * @return JsonResponse
     */
    public function store()
    {
        request()->validate([
            'name' => 'required|iunique:categories,name,null,null,organization_id,' . request()->organization()->id,
        ]);

        $category = Category::create([
            'name' => request('name'),
            'organization_id' => request()->organization()->id,
            'created_by' => auth()->user()->id
        ]);

        return new CategoryResource($category);
    }

    /**
     * Return a single category
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function show($hashid)
    {
        return new CategoryResource(
            Category::findOrFail(hashid($hashid))
        );
    }

    /**
     * Update a category
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function update($hashid)
    {
        $category = Category::findOrFail(hashid($hashid));

        request()->validate([
            'name' => "required|iunique:categories,name,{$category->id},id,organization_id," . request()->organization()->id,
        ]);

        $category->name = request('name');
        $category->save();

        return new CategoryResource($category);
    }

    /**
     * Remove a category from the database.
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function delete($hashid)
    {
        $category = Category::findOrFail(hashid($hashid));

        $category->delete();

        return response()->json([], 204);
    }
}
