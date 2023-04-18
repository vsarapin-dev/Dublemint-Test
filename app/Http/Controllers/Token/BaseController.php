<?php


namespace App\Http\Controllers\Token;


use App\Http\Controllers\Controller;
use App\Services\Token\Service;

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
