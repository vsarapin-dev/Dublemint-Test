<?php


namespace App\Services\SignIn;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Service class for signing in users
 */
class Service
{
    /**
     * Sign in user with personal access token
     *
     * @param Request $request The HTTP request object
     * @return \Symfony\Component\HttpFoundation\Response The Inertia response object
     */
    public function signIn(Request $request)
    {
        $token = $request['token'];
        $user = User::where('personal_access_token', $token)->first();
        Auth::login($user);

        return Inertia::location(route('home'));
    }
}
