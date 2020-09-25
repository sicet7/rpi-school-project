<?php

declare(strict_types=1);

use App\Utility\ConfigManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

use function DI\get;

return [
    FilesystemLoader::class => function (ConfigManager $config) {
        return new FilesystemLoader($config->get('twig.views'));
    },
    LoaderInterface::class => get(FilesystemLoader::class),
    Environment::class => function (LoaderInterface $loader, ConfigManager $config) {
        return new Environment($loader, $config->get('twig.environment_options'));
    }
];