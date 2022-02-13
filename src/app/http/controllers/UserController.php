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

        $r_fn = bin2hex(random_bytes(random_int(3, 5)));
        $r_ln = bin2hex(random_bytes(random_int(3, 5)));
        $r_un = 'un_' . bin2hex(random_bytes(random_int(3, 6)));
        $r_ps = bin2hex(random_bytes(random_int(3, 10)));
        $r_em = sprintf("%s@gmail.com", bin2hex(random_bytes(random_int(3, 5))));

        return Response::make()->view('auth.register', compact('token', 'r_fn', 'r_ln', 'r_un', 'r_ps', 'r_em'));
    }

    public function logout()
    {
        Auth::logoutTrigger();
        Redirect::next('home');
    }

    public function login(Request $request)
    {
        if (!$request->exists('post.username') && !$request->exists('post.password')) {
            Session::new()->flash('message', 'Login fields are empty!');
        } else {
            if (Auth::verify($request->post->username, $request->post->password)) {
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

        // $data['password'] = EndeCorder::HashedPassword($data['password']);

        $request->post->getEach(fn ($k, $v) => $user->$k = $v);

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
