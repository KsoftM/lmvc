<?php

namespace ksoftm\system\core;

require_once root . '/vendor/autoload.php';

use ksoftm\system\utils\io\FileManager;

class Env
{
    public static function load(string $path)
    {
        $f = FileManager::new($path);

        $data = explode(PHP_EOL, $f->read());

        foreach ($data as $key => $value) {
            if (!empty($value) && str_contains($value, '=')) {
                $data = explode('=', $value, 2);
                $_ENV[$data[0]] = $data[1];
            }
        }

        return $_ENV;
    }

    public static function get(string $name, $def = null)
    {
        return array_key_exists($name, $_ENV) ? $_ENV[$name] : $def;
    }
}
