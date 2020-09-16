<?php

namespace App\Actions;

use App\Database\Entities\TokenEntity;
use App\Interfaces\ActionInterface;
use App\Utility\ConfigManager;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorInterface;

class VersionAction implements ActionInterface
{

    /**
     * @var ConfigManager
     */
    private ConfigManager $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['POST', 'GET'], '/', static::class);
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $logDir = $this->configManager->get('directory.log');
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $postLogFile = $logDir . '/post-request.log';
        if (strtolower($request->getMethod()) === 'post') {
            file_put_contents($postLogFile, $request->getBody()->getContents() . PHP_EOL, FILE_APPEND);
        }
        $ipLogFile = $logDir . '/ip-request.log';
        file_put_contents($ipLogFile, $_SERVER['REMOTE_ADDR'] . PHP_EOL, FILE_APPEND);
        $response->getBody()->write('OK');
        return $response;
    }
}