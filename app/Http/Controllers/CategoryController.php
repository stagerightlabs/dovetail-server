<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Fetch a list of available categories
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return CategoryResource::collection($request->organization()->categories);
    }

    /**
     * Store a new category
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|iunique:categories,name,null,null,organization_id,' . $request->organization()->id,
        ], [
            'name.iunique' => 'This name is already in use'
        ]);

        $category = Category::create([
            'name' => $request->get('name'),
            'organization_id' => $request->organization()->id,
            'created_by' => $request->user()->id
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
     * @param  Request $request
     * @param string $hashid
     * @return JsonResponse
     */
    public function update(Request $request, $hashid)
    {
        $category = Category::findOrFail(hashid($hashid));

        $request->validate([
            'name' => "required|iunique:categories,name,{$category->id},id,organization_id," . $request->organization()->id,
        ]);

        $category->name = $request->get('name');
        $category->save();

        return new CategoryResource($category);
    }

    /**
     * Remove a category from the database.
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function destroy($hashid)
    {
        $category = Category::findOrFail(hashid($hashid));

        $category->delete();

        return response()->json([], 204);
    }
}
