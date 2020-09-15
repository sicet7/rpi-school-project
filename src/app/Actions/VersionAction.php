<?php

namespace App\Actions;

use App\Database\Entities\TokenEntity;
use App\Interfaces\ActionInterface;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorInterface;

class VersionAction implements ActionInterface
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['GET'], '/', static::class);
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $repository = $this->entityManager->getRepository(TokenEntity::class);
        $response->getBody()
            ->write('<pre>' . var_export($repository, true) . '</pre>');
        return $response;
    }
}