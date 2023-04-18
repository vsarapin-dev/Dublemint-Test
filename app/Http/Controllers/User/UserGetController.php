<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

class UserGetController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return $this->service->getAllUsers();
    }
}
