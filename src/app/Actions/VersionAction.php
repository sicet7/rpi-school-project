<?php

namespace App\Actions;

use App\Interfaces\ActionInterface;
use App\Utility\ConfigManager;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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

    public static function getMethods(): array
    {
        return ['GET'];
    }

    public static function getName(): ?string
    {
        return null;
    }

    public static function getPattern(): string
    {
        return '/';
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $this->configManager->get('database.password');
        $response->getBody()
            ->write('<pre>' . var_export($data, true) . '</pre>');
        return $response;
    }
}