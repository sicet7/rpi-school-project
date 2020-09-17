<?php

declare(strict_types=1);

namespace App\Factories;

use App\Interfaces\ActionFactoryInterface;
use App\Interfaces\ActionInterface;
use DI\DependencyException;
use DI\Factory\RequestedEntry;
use Invoker\ParameterResolver\ParameterResolver;

/**
 * Class ActionFactory
 * @package App\Factories
 */
class ActionFactory implements ActionFactoryInterface
{
    private const INTERFACE = ActionInterface::class;

    /**
     * @var ParameterResolver
     */
    private ParameterResolver $resolver;

    /**
     * ActionFactory constructor.
     * @param ParameterResolver $parameterResolver
     */
    public function __construct(ParameterResolver $parameterResolver)
    {
        $this->resolver = $parameterResolver;
    }

    /**
     * @param RequestedEntry $entry
     * @return ActionInterface
     * @throws DependencyException
     */
    public function create(RequestedEntry $entry): ActionInterface
    {
        $name = $entry->getName();

        if (!is_subclass_of($name, static::INTERFACE, true)) {
            throw new DependencyException(
                'Factory "' . static::class . '" cannot instantiate: "' . $name . '".'.
                'Requested Entry must implement: "' . static::INTERFACE . '".'
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
