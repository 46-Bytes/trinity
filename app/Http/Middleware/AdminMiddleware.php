<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */

    public function handle(Request $request, Closure $next) {
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            return $next($request);  // Continue for admin
        }

        return redirect('/user-dashboard');  // Redirect non-admins to user dashboard
    }
}
