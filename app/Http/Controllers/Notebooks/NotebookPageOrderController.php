<?php

namespace App\Http\Controllers\Notebooks;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class NotebookPageOrderController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request $request
     * @param  string $hashid
     * @return JsonResponse
     */
    public function update(Request $request, $hashid)
    {
        $this->requirePermission('notebooks.pages');

        $notebook = $request->organization()->notebooks()->findOrFail(hashid($hashid));

        $newPageOrder = collect(request('pages'));

        if ($newPageOrder->isEmpty()) {
            return response()->json(["error" => "No pages were indicated."], 422);
        }

        $notebook->pages->each(function ($page) use ($newPageOrder) {
            $page->sort_order = $newPageOrder->search($page->hashid);
            $page->save();
        });

        return response()->json([], 204);
    }
}
