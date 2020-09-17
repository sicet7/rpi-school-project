<?php

use App\Factories\ActionFactory;
use App\Factories\RepositoryFactory;
use App\Interfaces\ActionFactoryInterface;
use App\Interfaces\RepositoryFactoryInterface;
use DI\Invoker\FactoryParameterResolver;
use Doctrine\ORM\EntityManagerInterface;
use Invoker\ParameterResolver\ParameterResolver;
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
    //Parameter Resolver
    FactoryParameterResolver::class => create(FactoryParameterResolver::class)
        ->constructor(get(ContainerInterface::class)),
    ParameterResolver::class => get(FactoryParameterResolver::class),

    // PSR-17
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

    // Repository
    RepositoryFactory::class => create(RepositoryFactory::class)
        ->constructor(
            get(EntityManagerInterface::class),
            get(ParameterResolver::class)
        ),
    RepositoryFactoryInterface::class => get(RepositoryFactory::class),

    // Action
    ActionFactory::class => create(ActionFactory::class)
        ->constructor(get(ParameterResolver::class)),
    ActionFactoryInterface::class => get(ActionFactory::class),
];