<?php

use Phalcon\Mvc\Micro\Collection as MicroCollection;
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

/**
 * Add your routes here
 */
$app->get('/', function () {
    echo $this['view']->render('index');
});

$orders = new MicroCollection();

$orders->setHandler(new IndexController());
$orders->get('/', 'index');

$app->mount($orders);

/**
 * Not found handler
 */
$app->notFound(function () use($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
});
