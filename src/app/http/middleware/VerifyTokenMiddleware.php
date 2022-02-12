<?php

namespace ksoftm\app\http\middleware;

use Closure;
use ksoftm\system\core\Env;
use ksoftm\system\kernel\Request;
use ksoftm\system\kernel\Response;
use ksoftm\system\kernel\Route;
use ksoftm\system\middleware\MiddlewareFactory;
use ksoftm\system\utils\EndeCorder;

class VerifyTokenMiddleware implements MiddlewareFactory
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->isPostMethod()) {
            if ($request->post->haveKey('form_token')) {
                $key = EndeCorder::TokenValidate(
                    'form_token',
                    $request->post->form_token,
                    Env::get('APP_KEY')
                );
                if ($key->isValid() == false) {
                    Response::centeredMessage($key->getMessage());
                    exit;
                }
            }
        }

        return $next($request);
    }
}
