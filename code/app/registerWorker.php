<?php

/** @var \DI\Container $container */
use Smtt\Queue\WorkerInterface;
use Smtt\Service\InstantRegister;

$container = require_once(__DIR__.'/bootstrap.php');

$instantRegister = $container->get(InstantRegister::class);

$worker = $container->get(WorkerInterface::class);
$worker->addHandler($container->get('queue.name'), [$instantRegister, 'register']);

while($worker->handle());