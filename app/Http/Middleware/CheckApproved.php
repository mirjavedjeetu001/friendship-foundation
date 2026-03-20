<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Super admins and admins bypass approval check
            if ($user->hasRole(['super-admin', 'admin', 'accountant'])) {
                return $next($request);
            }
            
            // Check if member is approved
            if ($user->status !== 'approved') {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Your account is pending approval. Please wait for admin approval.');
            }
            
            // Check if active
            if (!$user->is_active) {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact admin.');
            }
        }
        
        return $next($request);
    }
}
