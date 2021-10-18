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


//<<----------->> testing <<----------->>//



$result = DB::insert(
    'users',
    function (Query $query) {
        $query->set([
            'firstName' => 'kajalan',
            'lastName' => 'sivarasa',
            'username' => 'vav-it-2019-f-0001',
            'password' => EndeCorder::new(Env::get('APP_KEY'))
                ->encrypt('password'),
            'email' => 'kajalan4@gmail.com',
            'role' => 'student',
            'active' => 1,
        ]);
    }
);



$result = DB::insert('users', fn ($q) => $q->set(['role_id' => 3]));




// select specified data from the user
$result = DB::select('users', function (Query $query) {
    $query->field(['firstName', 'username', 'email']);
    $query->where('username like ?', ['%2019%']);
    $query->group('age');
    $query->having('active = ?', [1]);
    $query->orderBy('firstName', true);
    $query->limit(10, 2);
});

// select all
$result = DB::select('users');


/*<<----------->> query <<----------->>

select employee.first_name,branch.branch_name
from employee
join branch
on employee.id = branch.mgr_id;

<<-----X----->> query <<-----X----->>*/

$result = DB::select('employee', function (Query $query) {
    $query->field(['employee.firstName', 'branch.name']);
    $query->join('employee.id', 'branch.mgr_id');
});



$result = DB::update('users', function (Query $query) {
    $query->set([
        'password' => EndeCorder::new(Env::get('APP_KEY'))
            ->encrypt('[pass]*92/23'),
    ])->where('username = ?', ['vav-it-2019-f-0001']);
});

$result = DB::delete('users', fn ($q) => $q->where(['active' => 0]));





Schema::CreateIfNotExists('users', function (RawQuery $query) {
    $query->id();
    $query->string('firstName', 50);
    $query->string('lastName', 50);
    $query->string('username', 50)->nullable()->unique();
    $query->string('email', 300)->unique();
    $query->string('password', 500);
    $query->year('batch');
    $query->boolean('active')->default(0);
    $query->integer('roles_id')->primaryKey()->foreignKey('roles.id');

    $query->timestamps();
});



    Schema::DropIfExists('users');



//<<-----X----->> testing <<-----X----->>//