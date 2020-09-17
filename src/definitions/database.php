<?php

use App\Database\EntityMapper;
use App\Interfaces\FinderFactoryInterface;
use App\Utility\ConfigManager;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration as ORMConfig;
use Doctrine\DBAL\Configuration as DBALConfig;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\Mapping\Driver\MappingDriver as MappingDriverInterface;

use function DI\create;
use function DI\get;
use function DI\factory;

return [
    // Mapping Driver
    EntityMapper::class => create(EntityMapper::class)
        ->constructor(
            get(ConfigManager::class),
            get(FinderFactoryInterface::class)
        ),
    MappingDriverInterface::class => get(EntityMapper::class),

    // EntityManager
    EventManager::class => create(EventManager::class),
    DBALConfig::class => create(DBALConfig::class),
    ORMConfig::class => function (ConfigManager $configManager, MappingDriverInterface $mappingDriver) {
        $configuration = Setup::createConfiguration(!$configManager->get('mode.production'));
        $configuration->setMetadataDriverImpl($mappingDriver);
        return $configuration;
    },
    Connection::class => function (ConfigManager $configManager, DBALConfig $configuration, EventManager $eventManager) {
        return DriverManager::getConnection(
            $configManager->get('database.params'),
            $configuration,
            $eventManager
        );
    },
    EntityManager::class => factory([EntityManager::class, 'create'])
        ->parameter('connection', get(Connection::class))
        ->parameter('config', get(ORMConfig::class))
        ->parameter('eventManager', get(EventManager::class)),
    EntityManagerInterface::class => get(EntityManager::class),
];
