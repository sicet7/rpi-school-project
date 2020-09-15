<?php

namespace App;

use App\Exceptions\BootstrappingException;
use App\Interfaces\ActionFactoryInterface;
use App\Interfaces\ActionInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function DI\factory;

class Bootstrap
{
    private static Bootstrap $instance;

    public static function runApplication(string $root): void
    {
        if (!isset(static::$instance)) {
            static::$instance = new static($root);
        }
        static::$instance->getSlim()->run();
    }

    private string $root;
    private App $application;
    private ContainerInterface $container;

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
     * @param App $app
     * @throws BootstrappingException
     */
    private function loadRoutes(App $app): void
    {
        foreach ($this->getActionFqns() as $class) {
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
     * @return string[]
     */
    private function getActionFqns(): array
    {
        $fqns = [];
        $finder = Finder::create();
        $finder->files()->in(__DIR__ . '/Actions')->name('*Action.php');
        if ($finder->hasResults()) {
            foreach ($finder as $fileInfo) {
                /** @var SplFileInfo $fileInof */

                $fqn = 'App/Actions/' . $fileInfo->getRelativePathname();
                $fqn = substr($fqn, 0, (strlen($fqn)-4));
                $fqn = str_replace('/', '\\', $fqn);

                if (is_subclass_of($fqn, ActionInterface::class, true)) {
                    $fqns[] = $fqn;
                }
            }
        }
        return $fqns;
    }
}