<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSupplierAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Super admin can access everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has supplier access
        if (!$user->supplier_id) {
            abort(403, 'No supplier access.');
        }

        // Check if supplier is active
        if (!$user->supplier || !$user->supplier->is_active) {
            abort(403, 'Supplier account is inactive.');
        }

        // Check if supplier subscription is active
        if (!$user->supplier->isSubscriptionActive()) {
            abort(403, 'Supplier subscription is inactive.');
        }

        return $next($request);
    }
}