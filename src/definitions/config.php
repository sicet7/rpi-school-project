<?php

use App\Utility\ConfigManager;
use Symfony\Component\Finder\Finder;

return [
    ConfigManager::class => function () {
        $manager = new ConfigManager();
        $finder = Finder::create();
        $finder->files()->in(dirname(__DIR__) . '/config')->name('*.php');
        if ($finder->hasResults()) {
            foreach ($finder as $fileInfo) {
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
