<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackUserOnlineStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userId = Auth::id();
            // Cache user online state for 2 minutes
            Cache::put('user-is-online-' . $userId, true, now()->addMinutes(2));
            // Keep track of the exact last seen Unix timestamp (expires in 7 days)
            Cache::put('user-last-seen-' . $userId, now()->timestamp, now()->addDays(7));
        }

        return $next($request);
    }
}
