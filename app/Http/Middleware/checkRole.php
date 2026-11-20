<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $role = (string)session('akses');
        if (in_array($role, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses Ditolak');
    }
}
