<?php

namespace ksoftm\system\console;

use ksoftm\system\Schema;
use ksoftm\system\console\Log;
use ksoftm\console\core\Command;
use ksoftm\system\utils\EndeCorder;
use ksoftm\console\core\CommandExtra;
use ksoftm\console\core\CommandFactory;
use ksoftm\system\utils\io\FileManager;
use ksoftm\system\utils\validator\MegRule;
use ksoftm\system\console\MakeTemplateFile;
use ksoftm\system\utils\validator\MegaValid;
use ksoftm\system\console\database\ApplyMigration;
use ksoftm\system\core\Env;

require_once './vendor/autoload.php';



class Make
{
    protected const SPECIAL_SEPARATOR = '___';

    public const FUNC_MIGRATION = 'make:migration'; // -a
    public const FUNC_CONTROLLER = 'make:controller'; // -c
    public const FUNC_MODEL = 'make:model'; // -m
    public const FUNC_MIDDLEWARE = 'make:middleware';

    public const FUNC_MIGRATE = 'migrate'; // [-r]
    public const FUNC_ENV_KEY = 'new:key';
    public const FUNC_RUN = 'run';

    public const FUNC_SHORT = [
        self::FUNC_MIGRATION => '-a',
        self::FUNC_CONTROLLER => '-c',
        self::FUNC_MODEL => '-m',
        self::FUNC_MIGRATE => '-r'
    ];

    protected static array $PATH = [];

    protected static array $TEMPLATE = [
        self::FUNC_MIGRATION => '/template/migration.template',
        self::FUNC_CONTROLLER => '/template/controller.template',
        self::FUNC_MODEL => '/template/model.template',
        self::FUNC_MIDDLEWARE => '/template/middleware.template',
    ];

    /**
     * Class constructor.
     */
    protected function __construct()
    {
    }

    public static function initPath(
        array $appPath = [
            self::FUNC_MIGRATION => '/migration',
            self::FUNC_CONTROLLER => '/controller',
            self::FUNC_MODEL => '/model',
            self::FUNC_ENV_KEY => '/.env',
        ]
    ): void {
        self::$PATH = $appPath;
    }

    public static function generateClassName(string $fileName): string|false
    {
        $regOut = MegaValid::validate([[$fileName, MegRule::new()->userName()]]);

        if ($regOut) {
            if (strpos($fileName, '_')) {
                return str_replace('_', '', ucwords($fileName, '_') ?? '');
            } else {
                return ucwords($fileName);
            }
        }
        return false;
    }

    public static function process(
        array $args,
        string $root,
        array $envKeyNames = ['APP_KEY', 'STORAGE_KEY']
    ): void {

        $cf = CommandFactory::getInstance();

        $runApp = Command::new(
            self::FUNC_RUN,
            'Run the development server',
            self::FUNC_RUN,
            fn () => self::runApp()
        );

        $newKey = Command::new(
            self::FUNC_ENV_KEY,
            'Generate new keyset for the application',
            self::FUNC_ENV_KEY,
            fn () => self::generateKey($root, $envKeyNames)
        );

        //<<----------->> migrate <<----------->>//

        $migrate = CommandExtra::new(
            self::FUNC_MIGRATE,
            'Apply the migrations',
            self::FUNC_MIGRATE,
            function ($d) use ($root) {
                if (empty($d)) {
                    return self::migrate($root);
                }
            }
        );

        $migrate->extras->register(
            Command::new(
                self::FUNC_SHORT[self::FUNC_MIGRATE],
                'Apply the rollback migrations',
                self::FUNC_SHORT[self::FUNC_MIGRATE],
                fn () => self::migrate($root, true)
            )
        );

        //<<-----X----->> migrate <<-----X----->>//

        //<<----------->> model <<----------->>//

        $makeModel = CommandExtra::new(
            self::FUNC_MODEL,
            "Make model class template",
            'make:model [name] [options]',
            fn ($name) => self::model($name, $root)
        );

        $makeModel->extras->register(
            Command::new(
                self::FUNC_SHORT[self::FUNC_CONTROLLER],
                'Make controller class template',
                self::FUNC_SHORT[self::FUNC_CONTROLLER],
                fn ($name) => self::controller($name, $root)
            )
        );

        $makeModel->extras->register(
            Command::new(
                self::FUNC_SHORT[self::FUNC_MIGRATION],
                'Make migration class template',
                self::FUNC_SHORT[self::FUNC_MIGRATION],
                fn ($name) => self::migration($name, $root)
            )
        );

        //<<-----X----->> model <<-----X----->>//


        //<<----------->> controller <<----------->>//

        $makeController = CommandExtra::new(
            self::FUNC_CONTROLLER,
            "Make controller class template",
            "make:controller [name] [options]",
            fn ($name) => self::controller($name, $root)
        );

        $makeController->extras->register(
            Command::new(
                self::FUNC_SHORT[self::FUNC_MODEL],
                'Make model class template',
                self::FUNC_SHORT[self::FUNC_MODEL],
                fn ($name) => self::model($name, $root)
            )
        );

        $makeController->extras->register(
            Command::new(
                self::FUNC_SHORT[self::FUNC_MIGRATION],
                'Make migration class template',
                self::FUNC_SHORT[self::FUNC_MIGRATION],
                fn ($name) => self::migration($name, $root)
            )
        );

        //<<-----X----->> controller <<-----X----->>//


        //<<----------->> migration <<----------->>//

        $makeMigration = CommandExtra::new(
            self::FUNC_MIGRATION,
            "Make migration class template",
            "make:migration [name] [options]",
            fn ($name) => self::migration($name, $root)
        );

        $makeMigration->extras->register(
            Command::new(
                self::FUNC_SHORT[self::FUNC_MODEL],
                'Make model class template',
                self::FUNC_SHORT[self::FUNC_MODEL],
                fn ($name) => self::model($name, $root)
            )
        );

        $makeMigration->extras->register(
            Command::new(
                self::FUNC_SHORT[self::FUNC_CONTROLLER],
                'Make controller class template',
                self::FUNC_SHORT[self::FUNC_CONTROLLER],
                fn ($name) => self::controller($name, $root)
            )
        );

        //<<-----X----->> migration <<-----X----->>//


        //<<----------->> middleware <<----------->>//

        $makeMiddleware = Command::new(
            self::FUNC_MIDDLEWARE,
            "Make middleware class template",
            "make:middleware [name] [options]",
            fn ($name) => self::middleware($name, $root)
        );

        //<<-----X----->> middleware <<-----X----->>//


        $cf->register($runApp);
        $cf->register($newKey);
        $cf->register($migrate);
        $cf->register($makeModel);
        $cf->register($makeController);
        $cf->register($makeMigration);
        $cf->register($makeMiddleware);

        $cf->registerHelp();

        log::block(function () use ($cf, $args) {
            $cf->run($args);
        });
    }

    public static function IsValidName(string $Name): bool
    {
        return MegaValid::validate([[$Name, MegRule::new()->userName()]]);
    }

    public static function migrate(string|false $root, bool $applyRoleBack = false): void
    {
        $path = $root . self::$PATH[self::FUNC_MIGRATION];

        try {
            if ($applyRoleBack == true) {
                Schema::safeKeyCheck(function () use ($path) {
                    ApplyMigration::applyRoleBackMigration($path);
                });
            } else {
                Schema::safeKeyCheck(function () use ($path) {
                    ApplyMigration::applyMigration($path);
                });
            }
        } catch (\Throwable $th) {
            echo 'Some error in the database connection.';
        }
    }

    public static function migration(string|false $migrationName, string|false $root): bool|string
    {
        if (self::IsValidName($migrationName)) {
            $path = $root . self::$PATH[self::FUNC_MIGRATION];
            $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_MIGRATION];

            $className = self::generateClassName($migrationName . '_migration');
            $uniqueFileName = self::createUniqueFileName($migrationName);

            return MakeTemplateFile::create($path, $className, $uniqueFileName, $templatePath, [
                '{className}' => $className
            ]);
        } else {
            return 'The name is invalid';
        }
    }

    public static function controller(string|false $controllerName, string|false $root): bool|string
    {
        if (self::IsValidName($controllerName)) {
            $path = $root . self::$PATH[self::FUNC_CONTROLLER];
            $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_CONTROLLER];
            $className = self::generateClassName($controllerName . '_controller');

            return MakeTemplateFile::create($path, $className, $className, $templatePath, [
                '{className}' => $className
            ]);
        } else {
            return 'The name is invalid';
        }
    }

    public static function model(string|false $modelName, string|false $root): bool|string
    {
        if (self::IsValidName($modelName)) {
            $path =  $root . self::$PATH[self::FUNC_MODEL];
            $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_MODEL];
            $className = self::generateClassName($modelName . "_model");

            return MakeTemplateFile::create($path, $className, $className, $templatePath, [
                '{className}' => $className
            ]);
        } else {
            return 'The name is invalid';
        }
    }

    public static function middleware(string|false $middlewareName, string|false $root): bool|string
    {
        if (self::IsValidName($middlewareName)) {
            $path =  $root . self::$PATH[self::FUNC_MIDDLEWARE];
            $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_MIDDLEWARE];
            $className = self::generateClassName($middlewareName . "_middleware");

            return MakeTemplateFile::create($path, $className, $className, $templatePath, [
                '{className}' => $className
            ]);
        } else {
            return 'The name is invalid';
        }
    }

    public static function generateKey(string|false $root, array $keyNames): bool|string
    {
        $path =  $root . self::$PATH[self::FUNC_ENV_KEY];
        $path = new FileManager($path);
        $lines = $path->readLines();

        foreach ($lines as $lineNo => $line) {
            foreach ($keyNames as $value) {
                if (str_contains(strtoupper($line), strtoupper($value))) {
                    $data = explode('=', $line, 2) + [null, null];
                    $data[1] = EndeCorder::generateUniqueKey();
                    $lines[$lineNo] = implode('=', $data);
                }
            }
        }

        $path->write(implode('', $lines));
        return "Keys Generated successfully." . PHP_EOL;
    }

    public static function getFileName(string $fileName): ?string
    {
        $path = (explode(
            self::SPECIAL_SEPARATOR,
            pathinfo($fileName, PATHINFO_FILENAME),
            2
        ) + [null, null])[1];

        return str_replace('_', '', ucwords(
            $path,
            '_'
        )) ?: pathinfo($fileName, PATHINFO_FILENAME);
    }

    public static function createUniqueFileName(string $migrationName): string
    {
        return date_create()->format('Y_m_d_G_i_s_u') . self::SPECIAL_SEPARATOR .
            $migrationName . '_migration';
    }

    public static function runApp(): void
    {
        $cmd = [
            'cd src/public/',
            sprintf('php -S %s:%d -F %s', Env::get('SERVER_NAME', 'localhost'), Env::get('SERVER_PORT', 2121), 'src/public/')
        ];

        echo exec(implode(' && ', $cmd));
    }
}
