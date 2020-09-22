<?php

declare(strict_types=1);

namespace App\Actions\Api\Entry;

use App\Database\Entities\Entry;
use App\Database\Repositories\EntryRepository;
use App\Interfaces\ActionInterface;
use App\Middleware\TokenMiddleware;
use App\Utility\CurrentToken;
use App\Utility\Json;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;
use Slim\Interfaces\RouteCollectorInterface;

class Post implements ActionInterface
{

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var CurrentToken
     */
    private CurrentToken $currentToken;

    /**
     * @var EntryRepository
     */
    private EntryRepository $entryRepository;

    public function __construct(Json $json, CurrentToken $currentToken, EntryRepository $entryRepository)
    {
        $this->json = $json;
        $this->currentToken = $currentToken;
        $this->entryRepository = $entryRepository;
    }

    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['POST'], '/api/entry', static::class)
            ->add(TokenMiddleware::class);
    }


    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            if (!$this->currentToken->isset()) {
                throw new \Exception('Token is not set.');
            }

            $token = $this->currentToken->get();
            $request->getBody()->rewind();
            $bodyString = $request->getBody()->getContents();
            Validator::json()->check($bodyString);
            $data = $this->json->decode($bodyString);
            Validator::allOf(
                Validator::arrayType(),
                Validator::notEmpty()
            )->check($data);

            $entry = new Entry($token, $data);

            $this->entryRepository->persist($entry);
            $this->entryRepository->flush();

            //FIXME: this is not ideal, but this was made quickly.
            return $response->withStatus(200)->withBody(Stream::create($this->json->encode([
                'id' => $entry->getId(),
                'data' => $entry->getData(),
                'token' => $entry->getToken()->getId(),
                'created_at' => $entry->getCreatedAt(true),
                'updated_at' => $entry->getUpdatedAt(true),
            ])));
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
            if ($throwable instanceof NestedValidationException) {
                $message = $throwable->getFullMessage();
            }
            return $response->withHeader('Content-Type', 'text/plain')->withStatus(400)->withBody(Stream::create($message));
        }
    }
}