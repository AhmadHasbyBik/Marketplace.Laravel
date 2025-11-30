<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $roleName = strtolower($role);

        if (
            ! Auth::check() ||
            ! $request->user()
                ->roles()
                ->whereRaw('LOWER(name) = ?', [$roleName])
                ->exists()
        ) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
