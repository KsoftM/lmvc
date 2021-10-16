<?php

require_once '../../vendor/autoload.php';

use ksoftm\system\kernel\Application;
use ksoftm\system\kernel\Route;

define('root', dirname(__DIR__, 2));
define('storage', root . "/storage");


$app = Application::getInstance();
$app->setRootPath(root);
$app->config();
$app->run();
