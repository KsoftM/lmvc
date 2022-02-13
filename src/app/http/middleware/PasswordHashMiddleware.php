<?php

namespace ksoftm\app\http\middleware;

use Closure;
use ksoftm\system\kernel\Request;
use ksoftm\system\kernel\Route;
use ksoftm\system\middleware\MiddlewareFactory;
use ksoftm\system\utils\EndeCorder;

class PasswordHashMiddleware implements MiddlewareFactory
{
    public function handle(Request $request, Closure $next): mixed
    {
        // if (isset($_POST['password']) && Route::currentRoutCheck('login') == false) {
        //     $_POST['password'] = EndeCorder::HashedPassword($_POST['password']);
        // }

        // if ($request->exists('password') && Route::currentRoutCheck('login') == false) {
        //     $request->post->password = EndeCorder::HashedPassword($request->post->password);
        // }

        if ($request->exists('post.password')) {
            $request->post->password = EndeCorder::HashedPassword($request->post->password);
            $request->loadData();
        }

        // if (isset($_GET['password'])) {
        //     $_GET['password'] = EndeCorder::HashedPassword($_GET['password']);
        // }

        return $next($request);
    }
}
