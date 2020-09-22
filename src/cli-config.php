<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Utility\ConfigManager;
use DI\ContainerBuilder;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;

$container = (new ContainerBuilder())
    ->addDefinitions(__DIR__ . '/definitions/utils.php')
    ->addDefinitions(__DIR__ . '/definitions/config.php')
    ->addDefinitions(__DIR__ . '/definitions/database.php')
    ->build();

$config = new ConfigurationArray($container->get(ConfigManager::class)->get('migrations'));

foreach ($container->get(ConfigManager::class)->get('migrations.migrations_paths') as $namespace => $directory) {
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
}

return DependencyFactory::fromEntityManager(
    $config,
    new ExistingEntityManager($container->get(EntityManagerInterface::class))
);