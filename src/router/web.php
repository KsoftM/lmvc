<?php

require_once root . '/vendor/autoload.php';

use ksoftm\app\http\controllers\HomeController;
use ksoftm\app\http\controllers\UserController;
use ksoftm\app\http\middleware\AuthMiddleware;
use ksoftm\system\kernel\Route;

Route::get('/', [HomeController::class, 'index'])
    ->name('home')->middleware([new AuthMiddleware]);

Route::get('/login', [UserController::class, 'loginPage'])->name('login');
Route::post('/login', [UserController::class, 'login']);

Route::get('/register', [UserController::class, 'registerPage'])->name('register');
Route::get('/register', [UserController::class, 'register']);

Route::get('/d/{name}/{hash}', [HomeController::class, 'download'])->name('download');
