<?php

namespace ksoftm\system\kernel;

use ksoftm\app\http\middleware\LangMiddleware;
use ksoftm\system\controller\Controller;
use ksoftm\system\core\Config;
use ksoftm\system\core\Env;
use ksoftm\system\database\connection\MySQLDataDrive;
use ksoftm\system\database\QueryBuilder;
use ksoftm\system\MDQ;
use ksoftm\system\middleware\MiddlewareStake;
use ksoftm\system\utils\html\BuildMixer;
use ksoftm\system\utils\html\Mixer;
use ksoftm\system\utils\io\FileManager;
use ksoftm\system\utils\SingletonFactory;


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

    /** @var string $rootPath root path of the application. */
    protected ?string $rootPath = null;
    /**
     * Class constructor.
     */
    protected function __construct()
    {
        //TODO: create instances of singletons in hear
    }

    /**
     * Get the value of rootPath
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * Set the value of rootPath
     *
     * @return  self
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;

        return $this;
    }

    public function config()
    {
        Env::load($this->getRootPath() . '/.env');

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

        $f = FileManager::new(root . '/src/app/lang');

        foreach ($f->getDirectoryFiles() as $value) {
            if ($value instanceof FileManager) {
                $lang[$value->getNameOnly()] = $value->includeOnce();
            }
        }

        Controller::config(root . '/resources/view', $lang);

        // TODO add error display settings

        //TODO:  configure the app hear

        request_dir(root . '/src/router');
    }


    public function run()
    {
        //TODO:  run the app hear
        MiddlewareStake::getInstance()
            ->add([new LangMiddleware()])
            ->handle(Request::getInstance());


        Route::build();
    }
}
