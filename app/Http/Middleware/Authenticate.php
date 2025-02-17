<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */ // app/Http/Middleware/Authenticate.php
    // app/Http/Middleware/Authenticate.php
    protected function redirectTo(Request $request): ?string
    {
        // Don't redirect for API requests
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // For web routes, you can keep the redirection if needed.
        return route('login');
    }
}
