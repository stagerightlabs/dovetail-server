<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CategoryUpdate;
use App\Http\Requests\CategoryCreation;
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
     * @param  CategoryCreation $request
     * @return JsonResponse
     */
    public function store(CategoryCreation $request)
    {
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
     * @param  CategoryUpdate $request
     * @param  string $hashid
     * @return JsonResponse
     */
    public function update(CategoryUpdate $request, $hashid)
    {
        $category = $request->input('category');
        $category->name = $request->input('name');
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
