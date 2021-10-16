<?php

namespace ksoftm\system\console\database;

use ksoftm\system\console\Make;
use ksoftm\system\utils\io\FileManager;
use ksoftm\system\utils\database\Migration;


class ApplyMigration
{
    public static function applyMigration(string $migrationDir): void
    {
        $f = new FileManager($migrationDir);
        foreach ($f->getDirectoryFiles(true) as $value) {
            if ($value instanceof FileManager) {
                $fileName = pathinfo($value->getPath(), PATHINFO_FILENAME);
                $class = Make::getFileName($fileName);

                if (!empty($class)) {
                    $value->require();
                    if (class_exists($class)) {
                        $class = new $class();
                        if ($class instanceof Migration) {
                            $class->applyMigration($fileName);
                        }
                    }
                }
            }
        }
    }

    public static function applyRoleBackMigration(string $migrationDir): void
    {
        $f = new FileManager($migrationDir);
        foreach ($f->getDirectoryFiles(true) as $value) {
            if ($value instanceof FileManager) {
                $fileName = pathinfo($value->getPath(), PATHINFO_FILENAME);
                $class = Make::getFileName($fileName);

                if (!empty($class)) {
                    $value->require();
                    if (class_exists($class)) {
                        $class = new $class();
                        if ($class instanceof Migration) {
                            $class->applyRoleBackMigration($fileName);
                        }
                    } else {
                    }
                }
            }
        }
    }
}
