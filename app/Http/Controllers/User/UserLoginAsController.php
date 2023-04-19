<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\LoginAsRequest;

class UserLoginAsController extends BaseController
{
    public function __invoke(LoginAsRequest $request)
    {
        $data = $request->validated();
        return $this->service->loginAs($data);
    }
}
