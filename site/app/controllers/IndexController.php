<?php

class IndexController extends \Phalcon\Mvc\Controller
{

    public function index()
    {
        var_dump('site response');
        var_dump(file_get_contents('http://users')); die;
    }
}

