<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string    $roles   // e.g. "Admin|HR"
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        // Must be logged in
        if (!Auth::check()) {
            abort(403, 'You are not logged in.');
        }

        // If the user doesn't have any of the roles, forbid access
        // We split $roles by "|" in case multiple roles are allowed
        $roleArray = explode('|', $roles);

        if (! Auth::user()->hasAnyRole($roleArray)) {
            abort(403, 'You do not have the required role(s).');
        }

        return $next($request);
    }
}
