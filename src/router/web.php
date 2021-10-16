<?php

require_once root . '/vendor/autoload.php';

use ksoftm\app\http\controllers\UserController;
use ksoftm\system\kernel\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/user/{id}/profile', [UserController::class, 'profile'])->name('user-profile');
