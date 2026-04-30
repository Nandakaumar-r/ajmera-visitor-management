<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, ...$permissions)
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized action.');
        }

        foreach ($permissions as $permission) {
            if (!$request->user()->hasPermissionTo($permission)) {
                abort(403, 'Unauthorized action.');
            }
        }

        return $next($request);
    }
}
