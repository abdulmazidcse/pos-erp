<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserStatus
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->status === 0) {
                // User's status is 0, return a response indicating they are not allowed
                return response()->json(['error' => 'User not allowed.'], 403);
            }
        }

        return $next($request);
    }

}
