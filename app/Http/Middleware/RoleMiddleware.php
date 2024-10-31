<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        
        // Check if the user is authenticated
        if (Auth::check()) {
            if (Auth::user()->role->role_name !== $role) {
                return redirect('/login')->withErrors(['email' => 'Unauthorized access.']);
            }
        } else {
            return redirect('/login')->withErrors(['email' => 'Please log in.']);
        }

        return $next($request);
    }

}
