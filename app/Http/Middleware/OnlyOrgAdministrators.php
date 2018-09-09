<?php

namespace App\Http\Middleware;

use Closure;
use App\AccessLevel;

class OnlyOrgAdministrators
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (request()->user()->access_level >= AccessLevel::$ORGANIZATION_ADMIN) {
            return $next($request);
        }

        // need to throw an exception here...
        //response()->json(['message' => "Permission Denied"], 403);
    }
}
