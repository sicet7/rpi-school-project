<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Database\Entities\Token;
use App\Database\Repositories\TokenRepository;
use App\Utility\CurrentToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

class TokenMiddleware implements MiddlewareInterface
{
    protected const HEADER = 'Authorization';
    /**
     * @var TokenRepository
     */
    private TokenRepository $tokenRepository;

    /**
     * @var CurrentToken
     */
    private CurrentToken $currentToken;

    public function __construct(TokenRepository $tokenRepository, CurrentToken $currentToken)
    {
        $this->tokenRepository = $tokenRepository;
        $this->currentToken = $currentToken;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws HttpUnauthorizedException
     * @throws HttpBadRequestException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasHeader(static::HEADER)) {
            throw new HttpBadRequestException($request, 'Bad request. Missing "' . static::HEADER . '" header.');
        }
        $authHeader = $request->getHeader(static::HEADER);
        if (empty($authHeader)) {
            throw new HttpBadRequestException($request, 'Bad request. Empty "' . static::HEADER . '" header.');
        }

        /*var_dump(Uuid::uuid4()->toString());
        die;*/

        foreach ($authHeader as $line) {
            $parts = preg_split('/\s+/', $line);
            if ($parts !== false && !empty($parts)) {
                $type = $parts[array_keys($parts)[0]];
                $value = $parts[array_keys($parts)[1]];
                if (strtolower($type) == 'bearer') {
                    $token = $this->tokenRepository->findByValue($value, false);
                    if ($token instanceof Token) {
                        $this->currentToken->set($token);
                        return $handler->handle($request);
                    }
                }
            }
        }
        throw new HttpUnauthorizedException($request);
    }
}