<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isEmployeeOnly()) {
            abort(403);
        }

        return $next($request);
    }
}
