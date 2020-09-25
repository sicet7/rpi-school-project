<?php

declare(strict_types=1);

namespace App\Actions;

use App\Interfaces\ActionInterface;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Twig\Environment;

class Charts implements ActionInterface
{

    /**
     * @var Environment
     */
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['GET'], '/', static::class);
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withBody(Stream::create(
                $this->twig->render('pages/charts.twig', [
                    'title' => 'Test Render'
                ])
            ));
    }
}