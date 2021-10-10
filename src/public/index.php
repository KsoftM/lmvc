<?php

require_once '../../vendor/autoload.php';

use ksoftm\system\kernel\Application;

define('root', dirname(__DIR__, 2));


$app = Application::getInstance();
$app->setRootPath(root);
$app->config();
$app->run();
