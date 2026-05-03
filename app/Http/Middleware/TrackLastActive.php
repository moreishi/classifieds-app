<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLastActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            $user->update(['last_active_at' => now()]);
        }

        return $next($request);
    }
}
