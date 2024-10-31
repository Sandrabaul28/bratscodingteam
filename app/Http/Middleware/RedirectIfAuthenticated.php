<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
         if (Auth::check()) {
            $user = Auth::user();
            switch ($user->role->role_name) {
                case 'Admin':
                    return redirect()->route('admin.dashboard');
                case 'User':
                    return redirect()->route('user.dashboard');
                case 'Aggregator':
                    return redirect()->route('aggregator.dashboard');
                default:
                    return redirect('/login'); // Default redirect if role not matched
            }
        }

        return $next($request);
    }

}
