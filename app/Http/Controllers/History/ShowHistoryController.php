<?php

namespace App\Http\Controllers\History;


class ShowHistoryController extends BaseController
{
    public function __invoke()
    {
        return $this->service->showHistory();
    }
}
