<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotebookResource;

class UserNotebookController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return NotebookResource::collection(
            $request->user()->availableNotebooks()->get()
        );
    }
}
