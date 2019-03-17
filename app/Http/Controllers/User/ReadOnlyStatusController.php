<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadOnlyStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        return new JsonResource([
            'readonly' => $request->user()->isReadOnly()
        ]);
    }
}
