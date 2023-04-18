<?php


namespace App\Services\SignIn;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class Service
{
    public function signIn(Request $request)
    {
        $token = $request['token'];
        $user = User::where('personal_access_token', $token)->first();
        Auth::login($user);

        return Inertia::location(route('home'));
    }
}
