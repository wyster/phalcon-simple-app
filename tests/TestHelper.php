<?php declare(strict_types=1);

namespace app\test;

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;

ini_set('display_errors', 'on');
error_reporting(E_ALL);

define('ROOT_PATH', __DIR__);

set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

include ROOT_PATH . '/../vendor/autoload.php';

$loader = new Loader();

include ROOT_PATH . '/../app/config/config.php';

$loader->register();

$di = new FactoryDefault();

Di::reset();

// Здесь можно добавить любые необходимые сервисы в контейнер зависимостей

Di::setDefault($di);
