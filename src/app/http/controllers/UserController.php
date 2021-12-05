<?php

namespace ksoftm\app\http\controllers;

use ksoftm\app\http\models\UserModel;
use ksoftm\system\controller\Controller;
use ksoftm\system\core\auth\Auth;
use ksoftm\system\core\Env;
use ksoftm\system\internal\DResult;
use ksoftm\system\kernel\Redirect;
use ksoftm\system\kernel\Request;
use ksoftm\system\kernel\Response;
use ksoftm\system\kernel\Route;
use ksoftm\system\utils\EndeCorder;
use ksoftm\system\utils\Session;

class UserController extends Controller
{
    public function loginPage()
    {
        $time = date_create('+20 minute')->getTimestamp();
        $token = EndeCorder::Token('form_token', Env::get('APP_KEY'), $time);

        return Response::make()->view('auth.login', compact('token'));
    }

    public function registerPage()
    {
        $time = date_create('+20 minute')->getTimestamp();
        $token = EndeCorder::Token('form_token', Env::get('APP_KEY'), $time);

        return Response::make()->view('auth.register', compact('token'));
    }

    public function logout()
    {
        Auth::logoutTrigger();
        Redirect::next('home');
    }

    public function login(Request $request)
    {
        if (is_null($request->username) || is_null($request->password)) {
            Session::new()->flash('message', 'Login was failed!');
        } else {
            if (Auth::verify($request->username, $request->password)) {
                // Session::new()->flash('message', 'Successfully logged in!');
                Redirect::next('home');
            } else {
                Session::new()->flash('message', 'Login was Failed!');
            }
        }
        Redirect::next('login');
    }

    public function register(Request $request)
    {
        $user = new UserModel;
        $data = $request->getMethodData(Route::POST_METHOD);

        // $data['password'] = EndeCorder::HashedPassword($data['password']);

        foreach ($data  as $key => $value) {
            $user->$key = $value;
        }

        if ($user->isValid()) {
            if (($res = $user->insert()) != false &&
                ($res instanceof DResult && $res->rowCount() == 1)
            ) {
                Session::new()->flash('message', 'Registered Successfully!');
                // Redirect::next('home');
                Redirect::next('register');
            } else {
                Session::new()->flash('message', 'Registration was Failed!');
                Redirect::next('register');
            }
        } else {
            $valid = $user->getErrors();

            Session::new()->flash('message', array_shift($valid));
            Redirect::next('register');
        }
    }
}
