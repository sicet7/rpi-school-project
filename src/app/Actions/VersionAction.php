<?php

declare(strict_types=1);

namespace App\Actions;

use App\Interfaces\ActionInterface;
use App\Utility\ConfigManager;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorInterface;

/**
 * Class VersionAction
 * @package App\Actions
 */
class VersionAction implements ActionInterface
{

    /**
     * @var ConfigManager
     */
    private ConfigManager $configManager;

    /**
     * VersionAction constructor.
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @param RouteCollectorInterface $routeCollector
     */
    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['POST', 'GET'], '/', static::class);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        var_dump($request->getHeader('Authorization'));
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
