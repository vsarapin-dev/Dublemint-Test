<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Models\Role;

class RoleController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'roles' => Role::select('id', 'name')->get(),
        ]);
    }
}
