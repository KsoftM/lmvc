<?php

namespace ksoftm\system\console;

use ksoftm\system\utils\io\FileManager;

class MakeTemplateFile
{
    public static function create(
        string $path,
        string $className,
        string $fileName,
        string $templatePath,
        array $replaces
    ): bool|string {
        $file = new FileManager($path);
        foreach ($file->getDirectoryFiles(true) as $value) {
            if ($value instanceof FileManager) {
                $name = Make::getFileName($value->getPath());
                $className = Make::getFileName($className);

                if ($className == $name && $value->contains(" $className ")) {
                    return "$className, must be a unique name." . PHP_EOL . PHP_EOL;
                }
            }
        }

        $file = new FileManager($path . "/$fileName.php");

        if (!$file->isExist()) {
            $data = new FileManager($templatePath);
            $data = $data->read();

            foreach ($replaces as $key => $value) {
                $data = str_replace($key, $value, $data);
            }

            if ($file->write($data, true)) {
                return "$className is created successfully." . PHP_EOL . PHP_EOL;
            } else {
                return "$className file is not created...!" . PHP_EOL . PHP_EOL;
            }
        }

        return false;
    }
}
