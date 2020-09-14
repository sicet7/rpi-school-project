<?php

use Psr\Http\Message\ResponseFactoryInterface;

use Slim\Factory\Psr17\NyholmPsr17Factory;
use Slim\Factory\Psr17\ServerRequestCreator;
use Slim\Interfaces\ServerRequestCreatorInterface;
use function DI\get;

return [
    ResponseFactoryInterface::class => get(NyholmPsr17Factory::class),
    ServerRequestCreatorInterface::class => get(ServerRequestCreator::class),
];