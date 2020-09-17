<?php

namespace App;

use App\Exceptions\BootstrappingException;
use App\Interfaces\ActionFactoryInterface;
use App\Interfaces\ActionInterface;
use App\Interfaces\RepositoryFactoryInterface;
use App\Interfaces\RepositoryInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function DI\factory;

/**
 * Class Bootstrap
 * @package App
 */
class Bootstrap
{
    /**
     * @var Bootstrap
     */
    private static Bootstrap $instance;

    /**
     * @param string $root
     * @throws BootstrappingException
     */
    public static function runApplication(string $root): void
    {
        if (!isset(static::$instance)) {
            static::$instance = new static($root);
        }
        static::$instance->getSlim()->run();
    }

    /**
     * @var string
     */
    private string $root;

    /**
     * @var App
     */
    private App $application;

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * Bootstrap constructor.
     * @param string $rootPath
     */
    private function __construct(string $rootPath)
    {
        $this->root = rtrim(trim($rootPath), '\\/');
    }

    /**
     * @return App
     * @throws BootstrappingException
     */
    private function getSlim(): App
    {
        if (!isset($this->application)) {
            $this->application = $this->newSlim();
        }
        return $this->application;
    }

    /**
     * @return ContainerInterface
     * @throws BootstrappingException
     */
    private function getContainer(): ContainerInterface
    {
        if (!isset($this->container)) {
            $this->container = $this->newContainer();
        }
        return $this->container;
    }

    /**
     * @return App
     * @throws BootstrappingException
     */
    private function newSlim(): App
    {
        $app = AppFactory::createFromContainer($this->getContainer());
        $this->loadRoutes($app);
        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(true,true,true);
        return $app;
    }

    /**
     * @return ContainerInterface
     * @throws BootstrappingException
     */
    private function newContainer(): ContainerInterface
    {
        try {
            $builder = new ContainerBuilder();
            $this->loadDefinitions($builder);
            return $builder->build();
        } catch (\Exception $exception) {
            throw new BootstrappingException($exception->getMessage(),$exception->getCode(), $exception);
        }
    }

    /**
     * @param App $app
     */
    private function loadRoutes(App $app): void
    {
        foreach ($this->getActionFqns() as $class) {
            /** @var ActionInterface $class */
            $class::register($app->getRouteCollector());
        }
    }

    /**
     * @param ContainerBuilder $builder
     * @throws BootstrappingException
     */
    private function loadDefinitions(ContainerBuilder $builder): void
    {
        $builder->useAnnotations(false);
        $builder->useAutowiring(false);
        $this->autowireActions($builder);
        $this->autowireRepositories($builder);
        $directory = $this->root . '/definitions';
        if (file_exists($directory) && is_dir($directory)) {
            $finder = Finder::create();
            $finder->files()->in($directory)->name('*.php');
            if ($finder->hasResults()) {
                foreach ($finder as $file) {
                    $builder->addDefinitions($file->getPathname());
                }
            }
            return;
        }
        throw new BootstrappingException(
            'Failed to load definitions. Directory does not exists: "' . $directory . '".'
        );
    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    private function autowireActions(ContainerBuilder $containerBuilder): void
    {
        $actionFqns = $this->getActionFqns();
        foreach ($actionFqns as $actionFqn) {
            $containerBuilder->addDefinitions([
                $actionFqn => factory([ActionFactoryInterface::class, 'create'])
            ]);
        }
    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    private function autowireRepositories(ContainerBuilder $containerBuilder): void
    {
        $repositoryFqns = $this->getRepositoryFqns();
        foreach ($repositoryFqns as $repositoryFqn) {
            $containerBuilder->addDefinitions([
                $repositoryFqn => factory([RepositoryFactoryInterface::class, 'create'])
            ]);
        }
    }

    /**
     * @return string[]
     */
    private function getActionFqns(): array
    {
        static $fqns;
        if (!isset($fqns)) {
            $fqns = [];
            $finder = Finder::create();
            $finder->files()->in($this->root . '/app/Actions')->name('*.php');
            if ($finder->hasResults()) {
                foreach ($finder as $fileInfo) {
                    /** @var SplFileInfo $fileInfo */

                    $fqn = 'App/Actions/' . $fileInfo->getRelativePathname();
                    $fqn = substr($fqn, 0, (strlen($fqn)-4));
                    $fqn = str_replace('/', '\\', $fqn);

                    if (is_subclass_of($fqn, ActionInterface::class, true)) {
                        $fqns[] = $fqn;
                    }
                }
            }
        }
        return $fqns;
    }

    /**
     * @return string[]
     */
    private function getRepositoryFqns(): array
    {
        static $fqns;
        if (!isset($fqns)) {
            $fqns = [];
            $finder = Finder::create();
            $finder->files()->in($this->root . '/app/Database/Repositories')->name('*.php');
            if ($finder->hasResults()) {
                foreach ($finder as $fileInfo) {
                    /** @var SplFileInfo $fileInfo */

                    $fqn = 'App/Database/Repositories/' . $fileInfo->getRelativePathname();
                    $fqn = substr($fqn, 0, (strlen($fqn)-4));
                    $fqn = str_replace('/', '\\', $fqn);

                    if (is_subclass_of($fqn, RepositoryInterface::class, true)) {
                        $fqns[] = $fqn;
                    }
                }
            }
        }
        return $fqns;
    }
}
