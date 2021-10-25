<?php

namespace ksoftm\app\http\controllers;

use ksoftm\app\http\models\UserModel;
use ksoftm\system\controller\Controller;
use ksoftm\system\core\auth\Auth;
use ksoftm\system\core\Env;
use ksoftm\system\database\Query;
use ksoftm\system\database\RawQuery;
use ksoftm\system\DB;
use ksoftm\system\kernel\Redirect;
use ksoftm\system\kernel\Request;
use ksoftm\system\kernel\Response;
use ksoftm\system\kernel\Route;
use ksoftm\system\model\BaseModel;
use ksoftm\system\Schema;
use ksoftm\system\utils\EndeCorder;
use ksoftm\system\utils\Session;

class UserController extends Controller
{
    public function loginPage()
    {
        return Response::make()->view('auth.login');
    }

    public function registerPage()
    {
        return Response::make()->view('auth.register');
    }

    public function login(Request $request)
    {
        if (empty($request->username) || empty($request->password)) {
            Session::new()->flash('message', 'Successfully logged in!');
        } else {
            if (Auth::verify($request->username, $request->password)) {
                Session::new()->flash('message', 'Successfully logged in!');
                Redirect::next('home');
            } else {
                Session::new()->flash('message', 'Login was Failed!');
            }
        }
        Redirect::next('login');
    }

    public function register(Request $request)
    {
        $model = new UserModel;
        $data = $request->getMethodData(Route::POST_METHOD);

        $data['password'] = EndeCorder::HashedPassword($data['password']);

        foreach ($data  as $key => $value) {
            $model->$key = $value;
        }

        if ($model->isValid()) {
            if ($model->insert() == false) {
                Session::new()->flash('message', 'Registration was Failed!');
                Redirect::next('home');
            } else {
                Session::new()->flash('message', 'Registered Successfully!');
                Redirect::next('register');
            }
        } else {
            $valid = $model->getErrors();
            Session::new()->flash('message', $valid[0]);
        }
    }
}