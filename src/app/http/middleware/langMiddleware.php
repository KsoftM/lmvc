<?php

namespace ksoftm\app\http\middleware;


use Closure;
use ksoftm\system\utils\Cookie;
use ksoftm\system\kernel\Request;
use ksoftm\system\middleware\MiddlewareFactory;

class LangMiddleware implements MiddlewareFactory
{
    public function handle(Request $request, Closure $next): mixed
    {

        if ($request->exists('lang')) {
            $cookie = Cookie::make('lang');
            $request->lang = $cookie->get();
        } else {
            $cookie = Cookie::make(
                'lang',
                'en',
                date_create('+1 year')->getTimestamp()
            )->start();
        }
        
        return $next($request);
    }
}
