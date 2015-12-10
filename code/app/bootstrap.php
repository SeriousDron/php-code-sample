<?php

use DI\ContainerBuilder;

require_once __DIR__.'/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAnnotations(true);

$cache = new Doctrine\Common\Cache\ApcCache();
$cache->setNamespace('SmttDefinitions');
$containerBuilder->setDefinitionCache($cache);

$containerBuilder->writeProxiesToFile(true, __DIR__.'/../storage/proxies');

$containerBuilder->addDefinitions(__DIR__ . '/config.php');
$container = $containerBuilder->build();

return $container;
