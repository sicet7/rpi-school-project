<?php

declare(strict_types=1);

namespace App\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorInterface;

/**
 * Interface ActionInterface
 * @package App\Interfaces
 */
interface ActionInterface
{
    /**
     * @param RouteCollectorInterface $routeCollector
     * @return void
     */
    public static function register(RouteCollectorInterface $routeCollector): void;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface;
}
