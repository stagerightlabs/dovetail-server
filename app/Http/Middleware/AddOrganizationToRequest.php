<?php

namespace App\Http\Middleware;

use Closure;

class AddOrganizationToRequest
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
        if (!$request->organization = $request->user()->organization) {
            return response()->json(['message' => 'There is a problem with your account'], 403);
        }

        return $next($request);
    }
}
