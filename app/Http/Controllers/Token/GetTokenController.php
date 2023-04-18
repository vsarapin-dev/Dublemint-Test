<?php

namespace App\Http\Controllers\Token;


class GetTokenController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return $this->service->getToken();
    }
}
