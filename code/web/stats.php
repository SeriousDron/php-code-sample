<?php

$container = require_once (__DIR__.'/../app/bootstrap.php');

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$response = $container->call(Smtt\Controller\Stats::class, [$request]);
$response->send();