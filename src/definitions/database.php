<?php

use App\Utility\ConfigManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

return [
    EntityManager::class => function (ConfigManager $configManager) {
        $config = $configManager->get('database');
        $isDevMode = !$configManager->get('mode.production');
    }
];
