<?php

namespace Za\Support\Permission\Middlewares;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (Auth::guest()) {
            throw new AuthenticationException('Unauthenticated.');
        }

        if (! Auth::user()->canAccess($permission)) {
            throw new AuthorizationException('Unauthorized.');
        }

        return $next($request);
    }
}
