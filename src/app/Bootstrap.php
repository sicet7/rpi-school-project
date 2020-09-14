<?php

namespace App;

use App\Exceptions\BootstrappingException;
use App\Interfaces\ActionInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Symfony\Component\Finder\Finder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Finder\SplFileInfo;

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
        $finder = Finder::create();
        $finder->files()->in(__DIR__ . '/Controllers')->name('*Action.php');
        if ($finder->hasResults()) {
            foreach ($finder as $fileInfo) {
                /** @var SplFileInfo $fileInof */

                $fqn = 'App/Controllers/' . $fileInfo->getRelativePathname();
                if (substr($fqn,-4) == '.php') {
                    $fqn = substr($fqn, 0, (strlen($fqn)-4));
                }
                $fqn = str_replace('/', '\\', $fqn);
                if (is_subclass_of($fqn, ActionInterface::class, true)) {
                    $methods = call_user_func([$fqn, 'getMethods']);
                    $pattern = call_user_func([$fqn, 'getPattern']);
                    $name = call_user_func([$fqn, 'getName']);

                    if (empty($methods)) {
                        throw new BootstrappingException(
                            'Failed to load routes.' .
                            'Empty methods array returned by: "' . $fqn . '::getMethods".'
                        );
                    }

                    array_walk($methods, function ($value, $key, $fqn) {
                        if (!is_string($value)) {
                            throw new BootstrappingException(
                                'Failed to load routes.' .
                                'Non-string method in array returned by: "' . $fqn . '::getMethods".'
                            );
                        }
                    }, $fqn);

                    if (!is_string($pattern)) {
                        throw new BootstrappingException(
                            'Failed to load routes.' .
                            'Non-string value return by: "' . $fqn . '::getPattern".'
                        );
                    }

                    $route = $app->getRouteCollector()->map($methods, $pattern, $fqn);
                    if (!empty($name) && is_string($name)) {
                        $route->setName($name);
                    }
                }
            }
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
}