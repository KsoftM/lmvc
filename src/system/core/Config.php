<?php

namespace ksoftm\system\core;

use ksoftm\system\utils\io\FileManager;

// configuration for config folder data
class Config
{
    public static function get(string $name, $default = null): mixed
    {
        if (str_contains($name, '.')) {
            $path = explode('.', $name);
            $name = array_shift($path);
        }

        $f = new FileManager(root . "/src/config/$name.php");

        if ($f->isExist()) {
            $data = include($f->getPath());

            if (isset($path) && is_array($path)) {
                foreach ($path as $value) {
                    if (key_exists($value, $data)) {
                        $data = $data[$value];
                    }
                }
            }
        } else {
            $data = false;
        }
        return $data;
    }
}
