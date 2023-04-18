<?php

namespace App\Http\Controllers\FeelingLucky;


class FeelingLuckyController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return $this->service->generateRandomValue();
    }
}
