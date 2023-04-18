<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnsureCodeIsPresent
{
    private ?string $token;
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $this->token = $request->route('token');

        if (User::where('personal_access_token', $this->token)->exists() &&
            $this->tokenNotExpired() &&
            $this->userIsActive())
        {
            return $next($request);
        }
        else
        {
            return to_route('index');
        }
    }

    private function tokenNotExpired(): bool
    {
        $expiresAt = User::where('personal_access_token', $this->token)->first()->expires_at;
        $currentDateTime = Carbon::now();
        $dateTimeExpired = Carbon::parse($expiresAt);

        if ($dateTimeExpired->gte($currentDateTime)) {
            return true;
        } else {
            return false;
        }
    }

    private function userIsActive(): bool
    {
        return User::where('personal_access_token', $this->token)->first()->is_active;
    }
}
