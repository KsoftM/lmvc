<?php

namespace ksoftm\app\http\controllers;

use ksoftm\system\controller\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->view('index');
    }
}
