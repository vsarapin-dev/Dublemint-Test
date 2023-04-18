<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EditUserRequest;
use Illuminate\Http\Request;

class UserEditController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EditUserRequest $request)
    {
        $data = $request->validated();
        return $this->service->editUser($data);
    }
}
