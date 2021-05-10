<?php

namespace App\Http\Controllers;

use App\Helpers\CustomJsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $json;

    function __construct(CustomJsonResponse $json)
    {
        $this->json = $json;
    }
}
