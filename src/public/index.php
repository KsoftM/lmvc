<?php

require_once '../../vendor/autoload.php';

use ksoftm\system\kernel\Application;

define('root', dirname(__DIR__, 2));
define('storage', root . "/storage");


$app = Application::getInstance();
$app->config();
$app->run();
