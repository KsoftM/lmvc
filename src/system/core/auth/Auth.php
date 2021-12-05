<?php


namespace ksoftm\system\core\auth;

use Exception;
use JetBrains\PhpStorm\Internal\ReturnTypeContract;
use ksoftm\system\core\Env;
use ksoftm\system\utils\Cookie;
use ksoftm\system\utils\Session;
use ksoftm\system\kernel\Response;
use ksoftm\system\model\BaseModel;
use ksoftm\system\utils\EndeCorder;
use ksoftm\app\http\models\UserModel;

class Auth
{
    /** @var string FINDER auth finder for cookie and session. */
    protected const FINDER = "login-status";

    /**
     * $identifier maybe contain username or email address password
     *
     * @param array $identifier
     *
     * @return bool
     */
    public static function verify(
        string $identifier,
        string $password,
        string $passwordFieldName = 'password',
        string $activeFieldName = 'active'
    ): bool {
        $model = new UserModel;
        $model->findAndLoad($identifier);
        $status = $model->$activeFieldName == '1' ? true : false;

        if (
            EndeCorder::VerifyHashedPassword($password, $model->$passwordFieldName) &&
            is_bool($status) && $status == true
        ) {
            self::loginTrigger($model);
            return true;
        } else {
            self::logoutTrigger();
            return false;
        }
    }

    // must be in the future
    public static function rememberMe(string $identifier, string $identifierName = 'username'): bool
    {
        $r = Cookie::make(
            $identifierName,
            $identifier,
            date_create('+1 year')->getTimestamp(),
        )->start();

        return $r == false ? false : true;
    }

    public static function isLoggedIn(): bool
    {
        if (!empty($token = Cookie::make(self::getName())->get())) {

            $token = EndeCorder::new(Env::get('APP_KEY'))->decrypt($token);
            $token = base64_decode($token);
            $token = (array) json_decode($token);

            try {
                if (EndeCorder::TokenValidate(
                    $token['id'],
                    $token['token'],
                    Env::get('APP_KEY')
                )->isValid()) {
                    // trigger the session for login
                    Session::new()->flash(self::getName(), true);
                    return true;
                }
            } catch (\Throwable $th) {
            }
        }
        Session::new()->flash(self::getName(), false);
        return false;
    }

    public static function user(): UserModel|false
    {
        if (!empty($token = Cookie::make(self::getName())->get())) {

            $token = base64_decode($token);

            $token = json_decode($token);

            try {
                if (EndeCorder::TokenValidate(
                    $token['id'],
                    $token['token'],
                    Env::get('APP_KEY')
                )->isValid()) {
                    $model = new UserModel;
                    $model->findAndLoad($token['id']);
                    return $model;
                }
            } catch (\Throwable $th) {
            }
        }
        return false;
    }

    public static function userId(): int|false
    {
        if (($model = self::user()) !== false) {
            $id = $model->getPrimaryKey();
            $id = $model->$id;
        }
        return $id ?? false;
    }

    public static function loginTrigger(BaseModel $model): bool
    {
        if (self::isLoggedIn()) {
            // return if it is available
            Session::new()->flash(self::getName(), true);
            return true;
        } else {
            // get the primary key
            $id = $model->getPrimaryKey();
            $id = $model->$id;

            // make the validation time
            $validTime = date_create('+5 days')->getTimestamp();

            // generate the token with time
            $token = EndeCorder::Token($id, Env::get('APP_KEY'), $validTime);

            // make the json toke data with id
            $token = json_encode(compact('id', 'token'), JSON_UNESCAPED_SLASHES);


            if (isset($id)) {
                // set the cookie if it is not available
                Cookie::make(
                    self::getName(),
                    EndeCorder::new(Env::get('APP_KEY'))->encrypt(base64_encode($token)),
                    $validTime,
                )->start();

                // trigger the session for login
                Session::new()->flash(self::getName(), true);
            } else {
                if (Env::isDebug()) {
                    throw new Exception("Primary Key does not assigned in the model!");
                }

                Response::make()->centeredMessage('Server error!');
            }

            return true;
        }
        return false;
    }

    public static function logoutTrigger(): void
    {
        try {
            // delete the login cookie
            Cookie::make(
                self::getName(),
                '',
                date_create('-2 week')->getTimestamp()
            )->start();

            // check the session for logout
            if (Session::new()->haveKey(self::getName())) {
                Session::new()->removeByKey(self::getName());
            }
        } catch (\Throwable $th) {
            if (Env::isDebug()) {
                throw $th;
            }
            Response::make()->centeredMessage('Authentication is Failed!');
        }
    }

    protected static function getName(): string
    {
        return EndeCorder::new(Env::get('APP_KEY'))->hash(self::FINDER);
    }
}
