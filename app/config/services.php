<?php declare(strict_types=1);

use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Simple as View;
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Translate\Adapter\NativeArray;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . '/config/config.php';
});

/**
 * Sets the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setViewsDir($config->application->viewsDir);
    return $view;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

$di->setShared(
    'session',
    function () {
        $session = new Session();

        $session->start();

        return $session;
    }
);

/**
 * @url https://docs.phalcon.io/3.4/ru-ru/translate
 */
$di->set(
    \Phalcon\Translate\AdapterInterface::class,
    function () {
        // Получение оптимального языка из браузера
        $language = $this['request']->getBestLanguage();

        $translationFile = APP_PATH . '/messages/' . $language. '.php';

        // Проверка существования перевода для полученного языка
        if (file_exists($translationFile)) {
            $messages = require $translationFile;
        } else {
            // Переключение на язык по умолчанию
            $messages = require APP_PATH . '/messages/en.php';
        }

        // Возвращение объекта работы с переводом
        return new NativeArray(
            [
                'content' => $messages,
            ]
        );
    }
);

$di->set(\Phalcon\Http\Client\Request::class, function () {
    return \Phalcon\Http\Client\Request::getProvider();
});
