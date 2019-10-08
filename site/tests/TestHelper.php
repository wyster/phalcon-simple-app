<?php

namespace Test;

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT_PATH', __DIR__);

set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

include ROOT_PATH . '/../vendor/autoload.php';

$loader = new Loader();

$config = include ROOT_PATH . '/../app/config/config.php';
var_dump($config->application->controllersDir);

$loader->registerDirs(
    [
        ROOT_PATH,
        $config->application->controllersDir,
        $config->application->modelsDir
    ]
)->register();

$loader->register();

$di = new FactoryDefault();

Di::reset();

// Здесь можно добавить любые необходимые сервисы в контейнер зависимостей

Di::setDefault($di);
