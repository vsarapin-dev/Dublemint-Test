<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\CreateUserRequest;

class UserCreateController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateUserRequest $request)
    {
        $data = $request->validated();
        return $this->service->createUser($data);
    }
}
