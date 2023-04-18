<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\DeleteUserRequest;

class UserDeleteController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DeleteUserRequest $request)
    {
        $data = $request->validated();
        return $this->service->deleteUser($data);
    }
}
