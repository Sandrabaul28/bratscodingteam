<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
use App\Models\Role;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()) {
            $role = roles::where('id', auth()->user()->roles)->first();
            if(Auth::user() && $role->role_name == 'Admin') {
                // return $next($request);
                dd($role->role_name);
            }
        }
        return redirect()->route('login');
    }
}
