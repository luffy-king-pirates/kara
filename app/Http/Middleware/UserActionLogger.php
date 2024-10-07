<?php

namespace App\Http\Middleware;

use App\Models\Logs;
use Closure;
use Stevebauman\Location\Facades\Location;

class UserActionLogger
{
    public function handle($request, Closure $next)
    {
        // Check if the request method is POST, PUT, or DELETE, but exclude POST for routes including 'logs' or 'logs/revert'
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])
            && !(str_contains($request->path(), 'logs/revert') && $request->method() == 'POST')) {

            // Get the authenticated user
            $user = auth()->user();

            // Capture the payload, excluding sensitive fields like passwords
            $payload = json_encode($request->except(['password', 'password_confirmation']));

            // Get the user's IP address
            $ip = $request->ip();


            // Log the action, user details, payload, IP address, and location
            Logs::create([
                'action' => $request->method() . ' ' . $request->path(),
                'user_name' => $user->name ?? 'Guest',
                'action_time' => now(),
                'payload' => $payload,
                'ip_address' => $ip,
               
            ]);

            // Maintain only 10 rows in the logs table
            $this->maintainLogSize();
        }

        return $next($request);
    }

    private function maintainLogSize()
    {
        // Keep only the latest 10 logs
        $logCount = Logs::count();
        if ($logCount > 10) {
            Logs::orderBy('id')->first()->delete(); // Delete the oldest log
        }
    }
}
