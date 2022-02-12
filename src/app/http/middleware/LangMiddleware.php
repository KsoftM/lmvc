<?php

namespace ksoftm\app\http\middleware;


use Closure;
use ksoftm\system\kernel\Redirect;
use ksoftm\system\utils\Cookie;
use ksoftm\system\kernel\Request;
use ksoftm\system\kernel\Response;
use ksoftm\system\middleware\MiddlewareFactory;

class LangMiddleware implements MiddlewareFactory
{
    public function handle(Request $request, Closure $next): mixed
    {

        if (!$request->exists('lang')) {
            Cookie::make(
                'lang',
                'en',
                date_create('+1 year')->getTimestamp()
            )->start(true);
        }

        return $next($request);
    }
}
