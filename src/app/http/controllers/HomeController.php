<?php

namespace ksoftm\app\http\controllers;

use ksoftm\system\controller\Controller;
use ksoftm\system\core\Env;
use ksoftm\system\kernel\Request;
use ksoftm\system\kernel\Response;
use ksoftm\system\utils\EndeCorder;
use ksoftm\system\utils\io\FileManager;

class HomeController extends Controller
{
    public function index()
    {
        $key = Env::get('STORAGE_KEY');
        // get the name of the file
        $name = 'sample.txt';
        try {
            $name = EndeCorder::new($key)->encrypt($name);
        } catch (\Throwable $th) {
            if (Env::isDebug()) {
                throw $th;
            }
            return Response::make()->centeredMessage('Encryption is failed!');
        }
        $time = date_create('+1 day')->getTimestamp();
        $hash = EndeCorder::Token($name, $key, $time);
        $url = route('download', compact('hash', 'name'));

        return Response::make()->view('index', compact('url'));
    }

    public function download(Request $request)
    {
        $key = Env::get('STORAGE_KEY');
        $name = $request->userRouterData()['name'];
        $hash = $request->userRouterData()['hash'];

        try {
            $token = EndeCorder::TokenValidate($name, $hash, $key);

            if ($token->isValid()) {
                $name = EndeCorder::new($key)->decrypt($name);
                return Response::make()->download(storage . "/$name");
            } else {
                return Response::make()->centeredMessage('Token is not valid!');
            }
        } catch (\Throwable $th) {
            if (Env::isDebug()) {
                throw $th;
            }

            return Response::make()->centeredMessage('Token validation failed!');
        }
    }
}
