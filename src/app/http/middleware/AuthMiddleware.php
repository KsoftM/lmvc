<?php

namespace ksoftm\app\http\middleware;

use Closure;
use ksoftm\system\core\auth\Auth;
use ksoftm\system\kernel\Redirect;
use ksoftm\system\kernel\Request;
use ksoftm\system\middleware\MiddlewareFactory;

class AuthMiddleware implements MiddlewareFactory
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::isLoggedIn() == false) {
            Redirect::next('login');
        }

        return $next($request);
    }
}
