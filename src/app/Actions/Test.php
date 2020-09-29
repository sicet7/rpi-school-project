<?php

declare(strict_types=1);

namespace App\Actions;

use App\Interfaces\ActionInterface;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorInterface;

/**
 * Class VersionAction
 * @package App\Actions
 */
class Test implements ActionInterface
{

    /**
     * @inheritDoc
     */
    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS', 'GET'], '/test', static::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $response->withStatus(204)->withBody(Stream::create(''));
    }
}
