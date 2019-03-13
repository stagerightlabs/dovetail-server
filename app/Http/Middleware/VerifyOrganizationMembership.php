<?php

namespace App\Http\Middleware;

use Closure;

class VerifyOrganizationMembership
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
        // Load the user's organization relationship
        $request->user()->load('organization');

        // Verify the organization exists
        if (!$request->user()->organization) {
            return response()->json(['message' => 'There is a problem with your account'], 403);
        }

        return $next($request);
    }
}
