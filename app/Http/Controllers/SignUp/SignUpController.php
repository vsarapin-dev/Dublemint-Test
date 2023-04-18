<?php

namespace App\Http\Controllers\SignUp;

use App\Http\Requests\SignUp\SignUpRequest;

class SignUpController extends BaseController
{
    public function __invoke(SignUpRequest $request)
    {
        $data = $request->validated();
        return $this->service->signUp($data);
    }
}
