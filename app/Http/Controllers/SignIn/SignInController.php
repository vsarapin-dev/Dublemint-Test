<?php

namespace App\Http\Controllers\SignIn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SignInController extends BaseController
{
    public function __invoke(Request $request)
    {
        return $this->service->signIn($request);
    }
}
