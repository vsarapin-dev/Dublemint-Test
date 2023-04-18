<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->userIsAdmin())
        {
            return to_route('home');
        }
        return $next($request);
    }

    private function userIsAdmin(): bool
    {
        return auth()->user() ?
            auth()->user()->hasRole('admin'):
            false;
    }
}
