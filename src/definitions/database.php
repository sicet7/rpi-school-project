<?php

use App\Database\EntityMapper;
use App\Interfaces\FinderFactoryInterface;
use App\Utility\ConfigManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\Mapping\Driver\MappingDriver as MappingDriverInterface;

use function DI\create;
use function DI\get;
use function DI\factory;

return [
    MappingDriverInterface::class => create(EntityMapper::class)
        ->constructor(
            get(ConfigManager::class),
            get(FinderFactoryInterface::class)
        ),
    Configuration::class => function (ConfigManager $configManager, MappingDriverInterface $mappingDriver) {
        $configuration = Setup::createConfiguration(!$configManager->get('mode.production'));
        $configuration->setMetadataDriverImpl($mappingDriver);
        return $configuration;
    },
    Connection::class => function (ConfigManager $configManager) {
        return DriverManager::getConnection($configManager->get('database.params'));
    },
    EntityManager::class => factory([EntityManager::class, 'create'])
        ->parameter('connection', get(Connection::class))
        ->parameter('config', get(Configuration::class)),
    EntityManagerInterface::class => get(EntityManager::class),
];
