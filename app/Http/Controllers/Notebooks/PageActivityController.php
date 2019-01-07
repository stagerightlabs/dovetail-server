<?php

namespace App\Http\Controllers\Notebooks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;

class PageActivityController extends Controller
{
    /**
     * Retrieve the activity log for a notebook page
     *
     * @param string $notebook
     * @param string $page
     * @return JsonResponse
     */
    public function show($notebook, $page)
    {
        $notebook = request()
                    ->organization()
                    ->notebooks()
                    ->findOrFail(hashid($notebook));

        return ActivityResource::collection(
            $notebook
                ->pages()
                ->findOrFail(hashid($page))
                ->activities()
                ->with('causer')
                ->get()
        );
    }
}
