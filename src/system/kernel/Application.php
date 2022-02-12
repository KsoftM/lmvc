<?php

namespace ksoftm\system\kernel;

require_once root . '/vendor/autoload.php';

use ksoftm\app\http\middleware\AuthMiddleware;
use ksoftm\system\MDQ;
use ksoftm\system\core\Env;
use ksoftm\system\core\Config;
use ksoftm\system\controller\Controller;
use ksoftm\system\utils\SingletonFactory;
use ksoftm\system\middleware\MiddlewareStake;
use ksoftm\app\http\middleware\LangMiddleware;
use ksoftm\app\http\middleware\PasswordHashMiddleware;
use ksoftm\app\http\middleware\VerifyTokenMiddleware;
use ksoftm\system\database\connection\MySQLDataDrive;
use ksoftm\system\utils\io\FileManager;
use ksoftm\system\utils\View;

class Application extends SingletonFactory
{
    protected static ?self $instance = null;
    public static function getInstance(): self
    {
        if (empty(self::$instance)) {
            self::$instance = parent::init($instance, self::class);
        }
        return self::$instance;
    }

    /**
     * Class constructor.
     */
    protected function __construct()
    {
        //TODO: create instances of singletons in hear
    }

    public function config()
    {
        Env::load(root . '/.env');

        $dbConn = Config::get('database.data')[Config::get('database.connection')];

        MDQ::Config(
            new MySQLDataDrive(
                $dbConn['host'],
                $dbConn['username'],
                $dbConn['password'],
                $dbConn['port'],
                $dbConn['database'],
            ),
            $dbConn['engine'],
            $dbConn['charset'],
            $dbConn['collation']
        );

        $f = new FileManager(root . "/src/app/lang");

        foreach ($f->getDirectoryFiles() as $value) {
            if ($value instanceof FileManager) {
                $lang[$value->getNameOnly()] = $value->requireOnce();
            }
        }

        // configure the rout files and language data
        View::config(root . '/resources/view', $lang);

        //<<----------->> errors config <<----------->>//
        switch (Env::get('APP_DEBUG', 'false')) {
            case 'true':
                // development version
                error_reporting(-1);
                ini_set('display_errors', 1);
                break;

            case 'false':
                // production version
                ini_set('display_errors', 0);
                if (version_compare(PHP_VERSION, '5.3', '>=')) {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
                } else {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
                }
                break;

            default:
                exit(3);
                break;
        }

        //<<-----X----->> errors config <<-----X----->>//


        //TODO:  configure the app in hear

        $f = new FileManager(root . '/src/router');
        if ($f->isExist()) {
            $f = $f->getDirectoryFiles(true);
            foreach ($f as $data) {
                if (
                    $data instanceof FileManager &&
                    strtolower(pathinfo($data->getName(), PATHINFO_EXTENSION)) == 'php'
                ) {
                    $data->requireOnce();
                }
            }
        }
    }


    public function run()
    {
        MiddlewareStake::getInstance()
            ->add([new LangMiddleware()])
            // ->add([new PasswordHashMiddleware()])
            ->add([new VerifyTokenMiddleware()])
            ->handle(Request::getInstance());


        Route::build();
    }
}
