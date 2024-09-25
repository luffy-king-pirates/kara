<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $resource)
    {
        // Check permissions for units
        if ($resource === 'units' && !$request->user()->can('admin')) {
            throw new NotFoundHttpException();  // 404 if unauthorized
        }

        // Check permissions for currencies
        if ($resource === 'currencies' && !$request->user()->can('admin')) {
            throw new NotFoundHttpException();  // 404 if unauthorized
        }

        return $next($request);
    }
}
