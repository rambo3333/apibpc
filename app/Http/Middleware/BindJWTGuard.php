<?php

namespace App\Http\Middleware;

use Closure;

class BindJWTGuard
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
        config(['auth.defaults.guard' => 'worker_api']);

        return $next($request);
    }
}
