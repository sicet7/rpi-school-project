<?php

declare(strict_types=1);

use App\Interfaces\FinderFactoryInterface;
use App\Utility\ConfigManager;
use Symfony\Component\Finder\SplFileInfo;

return [
    ConfigManager::class => function (FinderFactoryInterface $finderFactory) {
        $manager = new ConfigManager();
        $finder = $finderFactory->create();
        $finder->files()->in(dirname(__DIR__) . '/config')->name('*.php');
        if ($finder->hasResults()) {
            foreach ($finder as $fileInfo) {
                /** @var SplFileInfo */
                $data = require $fileInfo->getPathname();
                if (!is_array($data)) {
                    continue;
                }
                $path = $fileInfo->getRelativePathname();
                if (substr($path, -4) === '.php') {
                    $path = trim(substr($path, 0, (strlen($path)-4)), '/\\');
                }
                $manager->set(strtr($path, ['\\' => '.', '/' => '.']), $data);
            }
        }
        return $manager;
    },
];
