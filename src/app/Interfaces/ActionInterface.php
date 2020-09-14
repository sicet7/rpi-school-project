<?php

namespace App\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ActionInterface
{
    /**
     * @return string[]
     */
    public static function getMethods(): array;

    /**
     * @return string
     */
    public static function getPattern(): string;

    /**
     * @return string|null
     */
    public static function getName(): ?string;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface;
}