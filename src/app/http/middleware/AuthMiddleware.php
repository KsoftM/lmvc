<?php

namespace ksoftm\app\http\middleware;

use Closure;
use ksoftm\system\kernel\Request;
use ksoftm\system\middleware\MiddlewareFactory;

class AuthMiddleware implements MiddlewareFactory
{
    public function handle(Request $request, Closure $next): mixed
    {

        

        return $next($request);
    }
}
