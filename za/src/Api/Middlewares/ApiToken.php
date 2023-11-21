<?php

namespace Za\Support\Api\Middlewares;

use Closure;

class ApiToken
{
    /**
     * The URIs that should be accessible while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        $token = $request->header('X-API-TOKEN');
        if (! $token || ! isset(config('api_tokens')[$token])) {
            return response()->json(['message' => 'Invalid Api Token!'], 401);
        }

        // Set API Key's Application Name in container for Slack Error Message
        if (app()->has('slack')) {
            app('slack')->appName(config('api_tokens')[$token]);
        }

        return $next($request);
    }

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
