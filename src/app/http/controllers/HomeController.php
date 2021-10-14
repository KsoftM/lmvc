<?php

namespace ksoftm\app\http\controllers;

use ksoftm\system\controller\Controller;
use ksoftm\system\kernel\Response;

class HomeController extends Controller
{
    public function index()
    {
        return Response::make()->view('index');
    }
}
