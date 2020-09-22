<?php

declare(strict_types=1);

use App\Factories\FinderFactory;
use App\Interfaces\FinderFactoryInterface;
use App\Utility\CurrentToken;
use App\Utility\Json;

use function DI\create;

return [
    FinderFactoryInterface::class => create(FinderFactory::class),
    Json::class => create(Json::class)
        ->constructor(
            JSON_THROW_ON_ERROR |
            JSON_UNESCAPED_SLASHES |
            JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE,
            JSON_THROW_ON_ERROR |
            JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE |
            JSON_BIGINT_AS_STRING |
            JSON_OBJECT_AS_ARRAY
        ),
    CurrentToken::class => create(CurrentToken::class),
];
