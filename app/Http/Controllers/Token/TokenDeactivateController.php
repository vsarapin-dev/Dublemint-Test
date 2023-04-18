<?php

namespace App\Http\Controllers\Token;


class TokenDeactivateController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return $this->service->deactivate();
    }
}
