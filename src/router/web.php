<?php

require_once root . '/vendor/autoload.php';

use ksoftm\app\http\controllers\HomeController;
use ksoftm\app\http\controllers\UserController;
use ksoftm\app\http\middleware\AuthMiddleware;
use ksoftm\app\http\middleware\PasswordHashMiddleware;
use ksoftm\system\kernel\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/', [HomeController::class, 'lang']);


Route::get('/login', [UserController::class, 'loginPage'])->name('login');
Route::post('/login', [UserController::class, 'login']);

Route::get('/register', [UserController::class, 'registerPage'])->name('register');
Route::post('/register', [UserController::class, 'register'])
    ->middleware([new PasswordHashMiddleware]);

Route::get('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/d/{name}/{hash}', [HomeController::class, 'download'])
    ->name('download')->middleware([new AuthMiddleware]);

// Route::get('/lang/{source}', [HomeController::class, 'lang'])->name('lang');
Route::post('/lang', [HomeController::class, 'lang'])->name('lang');
