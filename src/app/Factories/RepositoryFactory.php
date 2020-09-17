<?php

declare(strict_types=1);

namespace App\Factories;

use App\Interfaces\RepositoryFactoryInterface;
use App\Interfaces\RepositoryInterface;
use DI\DependencyException;
use DI\Factory\RequestedEntry;
use Doctrine\ORM\EntityManagerInterface;
use Invoker\ParameterResolver\ParameterResolver;

/**
 * Class RepositoryFactory
 * @package App\Factories
 */
class RepositoryFactory implements RepositoryFactoryInterface
{
    private const INTERFACE = RepositoryInterface::class;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var ParameterResolver
     */
    private ParameterResolver $resolver;

    /**
     * RepositoryFactory constructor.
     * @param EntityManagerInterface $entityManager
     * @param ParameterResolver $parameterResolver
     */
    public function __construct(EntityManagerInterface $entityManager, ParameterResolver $parameterResolver)
    {
        $this->entityManager = $entityManager;
        $this->resolver = $parameterResolver;
    }

    /**
     * @param RequestedEntry $entry
     * @return RepositoryInterface
     * @throws DependencyException
     */
    public function create(RequestedEntry $entry): RepositoryInterface
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
                $instance = new $name(...$args);
            } else {
                $instance = new $name();
            }
            /** @var RepositoryInterface $instance */
            $instance->setEntityManager($this->entityManager);
            return $instance;
        } catch (\ReflectionException $exception) {
            throw new DependencyException($exception->getMessage(), $exception->getCode(), $exception);
        }

    }
}
