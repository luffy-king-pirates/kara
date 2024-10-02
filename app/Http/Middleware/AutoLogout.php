<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AutoLogout
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = Session::get('lastActivityTime');
            if ($lastActivity) {
                // Calculate the time difference
                $inactiveTime = time() - $lastActivity;

                // Logout if inactive for more than 60 seconds
                if ($inactiveTime > 60) {
                    Auth::logout();
                    return redirect('/login')->with('message', 'You have been logged out due to inactivity.');
                }
            }

            // Update last activity time
            Session::put('lastActivityTime', time());
        }

        return $next($request);
    }
}
