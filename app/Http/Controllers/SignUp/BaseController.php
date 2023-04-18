<?php


namespace App\Http\Controllers\SignUp;


use App\Http\Controllers\Controller;
use App\Services\SignUp\Service;

class BaseController extends Controller
{
    protected Service $service;

    /**
     * BaseController constructor.
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }
}
