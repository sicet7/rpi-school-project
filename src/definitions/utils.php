<?php

use App\Factories\FinderFactory;
use App\Interfaces\FinderFactoryInterface;

use function DI\create;

return [
    FinderFactoryInterface::class => create(FinderFactory::class)
];