<?php

require_once root . '/vendor/autoload.php';

use ksoftm\app\http\controllers\HomeController;
use ksoftm\system\kernel\Response;
use ksoftm\system\kernel\Route;

Route::get('/', [HomeController::class, 'index']);
