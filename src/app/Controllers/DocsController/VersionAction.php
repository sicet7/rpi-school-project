<?php

namespace App\Controllers\DocsController;

use App\Interfaces\ActionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class VersionAction implements ActionInterface
{

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
        return '/version';
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write('Version 1.0');
        return $response;
    }
}