<?php

require_once root . '/vendor/autoload.php';

use ksoftm\app\http\controllers\HomeController;
use ksoftm\app\http\controllers\UserController;
use ksoftm\system\kernel\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/d/{name}/{hash}', [HomeController::class, 'download'])->name('download');
