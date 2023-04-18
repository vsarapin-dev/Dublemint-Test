<?php

namespace App\Http\Controllers\Token;


class TokenRegenerateController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return $this->service->regenerate();
    }
}
