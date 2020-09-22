<?php

declare(strict_types=1);

use App\Database\Repositories\TokenRepository;
use App\Middleware\TokenMiddleware;

use App\Utility\CurrentToken;
use function DI\create;
use function DI\get;

return [
    TokenMiddleware::class => create(TokenMiddleware::class)
        ->constructor(
            get(TokenRepository::class),
            get(CurrentToken::class)
        )
];