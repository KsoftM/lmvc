<?php

namespace ksoftm\system\console;

use ksoftm\system\Schema;
use ksoftm\system\console\Log;
use ksoftm\system\utils\EndeCorder;
use ksoftm\system\utils\io\FileManager;
use ksoftm\system\utils\validator\MegRule;
use ksoftm\system\console\MakeTemplateFile;
use ksoftm\system\utils\validator\MegaValid;
use ksoftm\system\console\database\ApplyMigration;

require_once './vendor/autoload.php';



class Make
{
    protected const SPECIAL_SEPARATOR = '___';

    public const FUNC_MIGRATION = 'make:migration'; // -a
    public const FUNC_CONTROLLER = 'make:controller'; // -c
    public const FUNC_MODEL = 'make:model'; // -m
    public const FUNC_MIGRATE = 'migrate'; // [-r]

    public const FUNC_ENV_KEY = 'new:key';
    public const FUNC_MIDDLEWARE = 'make:middleware';

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

        if (!empty($args))
            $func = array_shift($args);
        if (!empty($args) && is_array($args)) {
            $optional = array_shift($args);
        }

        if (empty($func) || !in_array($func, [
            self::FUNC_MIGRATION,
            self::FUNC_CONTROLLER,
            self::FUNC_MODEL,
            self::FUNC_ENV_KEY,
            self::FUNC_MIGRATE,
            self::FUNC_MIDDLEWARE
        ])) {
            Log::BlogLog("Invalid argument function passed.");
            exit;
        }
        if (!empty($optional) && is_array($optional)) {
            $optional = array_shift($optional);
        } else {
            $optional = $optional ?? [];
        }

        log::block(function () use ($optional, $func, $root, $args, $envKeyNames) {
            if (
                $func == self::FUNC_MIGRATION ||
                (is_array($args) &&
                    in_array(self::FUNC_SHORT[self::FUNC_MIGRATION], $args))
            ) {
                if (!empty($optional) && self::IsValidName($optional))
                    self::migration($optional, $root);
                else
                    echo "Invalid argument passed." . PHP_EOL;
            }

            if (
                $func == self::FUNC_CONTROLLER ||
                (is_array($args) &&
                    in_array(self::FUNC_SHORT[self::FUNC_CONTROLLER], $args))
            ) {
                if (!empty($optional) && self::IsValidName($optional))
                    self::controller($optional, $root);
                else
                    echo "Invalid argument passed." . PHP_EOL;
            }

            if (
                $func == self::FUNC_MODEL ||
                (is_array($args) &&
                    in_array(self::FUNC_SHORT[self::FUNC_MODEL], $args))
            ) {
                if (!empty($optional) && self::IsValidName($optional))
                    self::model($optional, $root);
                else
                    echo "Invalid argument passed." . PHP_EOL;
            }

            if ($func == self::FUNC_MIDDLEWARE) {
                if (!empty($optional) && self::IsValidName($optional))
                    self::middleware($optional, $root);
                else
                    echo "Invalid argument passed." . PHP_EOL;
            }

            if ($func == self::FUNC_ENV_KEY) {
                self::generateKey($root, $envKeyNames);
            }

            if (
                $func == self::FUNC_MIGRATE ||
                (is_array($args) &&
                    in_array(self::FUNC_SHORT[self::FUNC_MIGRATE], $args))
            ) {
                self::migrate($root, $optional ?? false);
            }
        });
    }

    public static function IsValidName(string $Name): bool
    {
        return MegaValid::validate([[$Name, MegRule::new()->userName()]]);
    }

    public static function migrate(string|false $root, array|string|false $optional): void
    {
        $path = $root . self::$PATH[self::FUNC_MIGRATION];

        if ($optional == '-r') {
            Schema::safeKeyCheck(function () use ($path) {
                ApplyMigration::applyRoleBackMigration($path);
            });
        } else {
            Schema::safeKeyCheck(function () use ($path) {

                ApplyMigration::applyMigration($path);
            });
        }
    }

    public static function migration(string|false $migrationName, string|false $root): void
    {
        $path = $root . self::$PATH[self::FUNC_MIGRATION];
        $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_MIGRATION];

        $className = self::generateClassName($migrationName . '_migration');
        $uniqueFileName = self::createUniqueFileName($migrationName);

        MakeTemplateFile::create($path, $className, $uniqueFileName, $templatePath, [
            '{className}' => $className
        ]);
    }

    public static function controller(string|false $controllerName, string|false $root): void
    {
        $path = $root . self::$PATH[self::FUNC_CONTROLLER];
        $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_CONTROLLER];
        $className = self::generateClassName($controllerName . '_controller');

        MakeTemplateFile::create($path, $className, $className, $templatePath, [
            '{className}' => $className
        ]);
    }

    public static function model(string|false $modelName, string|false $root): void
    {
        $path =  $root . self::$PATH[self::FUNC_MODEL];
        $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_MODEL];
        $className = self::generateClassName($modelName . "_model");

        MakeTemplateFile::create($path, $className, $className, $templatePath, [
            '{className}' => $className
        ]);
    }

    public static function middleware(string|false $middlewareName, string|false $root): void
    {
        $path =  $root . self::$PATH[self::FUNC_MIDDLEWARE];
        $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_MIDDLEWARE];
        $className = self::generateClassName($middlewareName . "_middleware");

        MakeTemplateFile::create($path, $className, $className, $templatePath, [
            '{className}' => $className
        ]);
    }

    public static function generateKey(string|false $root, array $keyNames): void
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
        echo "Keys Generated successfully." . PHP_EOL;
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
}
