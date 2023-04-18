<?php


namespace App\Http\Controllers\SignIn;


use App\Http\Controllers\Controller;
use App\Services\SignIn\Service;

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
