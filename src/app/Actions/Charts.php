<?php

declare(strict_types=1);

namespace App\Actions;

use App\Interfaces\ActionInterface;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Twig\Environment;

/**
 * Class Charts
 * @package App\Actions
 */
class Charts implements ActionInterface
{

    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * Charts constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @inheritDoc
     */
    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['GET'], '/', static::class);
    }

    /**
     * @inheritDoc
     */
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