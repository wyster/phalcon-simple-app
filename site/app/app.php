<?php

use Phalcon\Mvc\Micro\Collection as MicroCollection;
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */


$index = new MicroCollection();

$index->setHandler(new IndexController());
$index->get('/', 'index');
$index->post('/', 'auth');

$app->mount($index);

/**
 * Not found handler
 */
$app->notFound(function () use($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
});
