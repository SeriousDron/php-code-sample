<?php

use Psr\Log\LoggerInterface;
use Monolog\Logger;

return [
    'log.path'  => __DIR__.'/../log/smtt.log',
    'log.level' => Logger::WARNING,


    Smtt\RegisterMo\RegisterMoInterface::class => \DI\object(Smtt\RegisterMo\QueueRegister::class),

    LoggerInterface::class => DI\object(Logger::class)
        ->constructor('Smtt')
        ->method('pushHandler', DI\get('log.handler'))
        ->method('pushProcessor', DI\object(\Monolog\Processor\WebProcessor::class))
        ->lazy(),
    'log.formatter' => DI\object(\Monolog\Formatter\LineFormatter::class)
    ->constructor("[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"),
    'log.handler'   => DI\object(\Monolog\Handler\StreamHandler::class)->constructor(DI\get('log.path'), DI\get('log.level'))
    ->method('setFormatter', DI\get('log.formatter')),


];