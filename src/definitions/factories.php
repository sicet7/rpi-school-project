<?php

use App\Factories\ActionFactory;
use App\Interfaces\ActionFactoryInterface;
use DI\Invoker\FactoryParameterResolver;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Http\Message\UriFactoryInterface;

use function DI\get;
use function DI\create;

return [
    Psr17Factory::class => create(Psr17Factory::class),
    RequestFactoryInterface::class => get(Psr17Factory::class),
    ResponseFactoryInterface::class => get(Psr17Factory::class),
    ServerRequestFactoryInterface::class => get(Psr17Factory::class),
    StreamFactoryInterface::class => get(Psr17Factory::class),
    UploadedFileFactoryInterface::class => get(Psr17Factory::class),
    UriFactoryInterface::class => get(Psr17Factory::class),
    ServerRequestCreatorInterface::class => create(ServerRequestCreator::class)
        ->constructor(
            get(ServerRequestFactoryInterface::class),
            get(UriFactoryInterface::class),
            get(UploadedFileFactoryInterface::class),
            get(StreamFactoryInterface::class)
        ),
    FactoryParameterResolver::class => create(FactoryParameterResolver::class)
        ->constructor(get(ContainerInterface::class)),
    ActionFactoryInterface::class => create(ActionFactory::class)
        ->constructor(get(FactoryParameterResolver::class))
];