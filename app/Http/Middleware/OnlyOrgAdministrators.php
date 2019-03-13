<?php

namespace App\Http\Middleware;

use Closure;
use App\AccessLevel;
use Illuminate\Auth\Access\AuthorizationException;

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
        if ($request->user()->access_level >= AccessLevel::$ORGANIZATION_ADMIN) {
            return $next($request);
        }

        throw new AuthorizationException("This action is unauthorized.");
    }
}
