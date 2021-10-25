<?php

namespace ksoftm\system\core;

require_once root . '/vendor/autoload.php';

use ksoftm\system\utils\io\FileManager;

class Env
{
    public static function load(string $path)
    {

        $f = new FileManager($path);

        $data = $f->readLines();

        foreach ($data as $value) {
            if (!empty($value) && str_contains($value, '=')) {
                $data = explode('=', $value, 2);
                $_ENV[trim($data[0])] = trim($data[1]);
            }
        }

        return $_ENV;
    }

    public static function get(string $name, $def = null)
    {
        return array_key_exists($name, $_ENV) ? $_ENV[$name] : $def;
    }

    public static function isDebug(): bool
    {
        if (Env::get('APP_DEBUG', false) == 'false') {
            return false;
        }
        return true;
    }
}
