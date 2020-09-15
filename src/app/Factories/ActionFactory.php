<?php

namespace App\Factories;

use App\Interfaces\ActionFactoryInterface;
use App\Interfaces\ActionInterface;
use DI\DependencyException;
use DI\Factory\RequestedEntry;
use DI\Invoker\FactoryParameterResolver;

class ActionFactory implements ActionFactoryInterface
{

    /**
     * @var FactoryParameterResolver
     */
    private FactoryParameterResolver $resolver;

    public function __construct(FactoryParameterResolver $factoryParameterResolver)
    {
        $this->resolver = $factoryParameterResolver;
    }

    /**
     * @param RequestedEntry $entry
     * @return ActionInterface
     * @throws DependencyException
     */
    public function create(RequestedEntry $entry): ActionInterface
    {
        $name = $entry->getName();

        if (!is_subclass_of($name, ActionInterface::class, true)) {
            throw new DependencyException(
                'Factory "' . static::class . '" cannot instantiate: "' . $name . '".'.
                'Requested Entry must implement: "' . ActionInterface::class . '".'
            );
        }
        try {
            if (method_exists($name, '__construct')) {
                $args = $this->resolver->getParameters(
                    new \ReflectionMethod($name, '__construct'),
                    [],
                    []
                );
                return new $name(...$args);
            } else {
                return new $name();
            }
        } catch (\ReflectionException $exception) {
            throw new DependencyException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}