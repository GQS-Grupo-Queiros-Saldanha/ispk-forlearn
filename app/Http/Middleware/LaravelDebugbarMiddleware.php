<?php

namespace App\Http\Middleware;

use Closure;

class LaravelDebugbarMiddleware
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
        //Disable debugbar if not a developer
        if (!in_array($request->ip(), ['bl15-185-120.dsl.telepac.pt', '188.80.185.120', '::1', '127.0.0.1'], true)) {
            config(['app.debug' => false]);
        }
        return $next($request);
    }
}
