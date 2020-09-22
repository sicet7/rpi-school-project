<?php

declare(strict_types=1);

namespace App\Actions\Api\Entry;

use App\Database\Repositories\EntryRepository;
use App\Interfaces\ActionInterface;
use App\Utility\Json;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Slim\Interfaces\RouteCollectorInterface;

class Get implements ActionInterface
{

    /**
     * @var EntryRepository
     */
    private EntryRepository $entryRepository;
    /**
     * @var Json
     */
    private Json $json;

    public function __construct(EntryRepository $entryRepository, Json $json)
    {
        $this->entryRepository = $entryRepository;
        $this->json = $json;
    }

    /**
     * @inheritDoc
     */
    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['GET'], '/api/entry/{id}', static::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            Validator::allOf(
                Validator::arrayType(),
                Validator::notEmpty(),
                Validator::keySet(
                    Validator::key('id', Validator::allOf(
                        Validator::stringType(),
                        Validator::notEmpty(),
                        Validator::uuid(4)
                    ))
                )
            )->check($args);

            $id = $args['id'];

            $entry = $this->entryRepository->getById($id);

            //FIXME: this is not ideal, but this was made quickly.
            return $response->withStatus(200)->withBody(Stream::create(
                $this->json->encode([
                    'id' => $entry->getId(),
                    'data' => $entry->getData(),
                    'token' => $entry->getToken()->getId(),
                    'created_at' => $entry->getCreatedAt(true),
                    'updated_at' => $entry->getUpdatedAt(true),
                ])
            ));
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
            if ($throwable instanceof NestedValidationException) {
                $message = $throwable->getFullMessage();
            }
            return $response->withHeader('Content-Type', 'text/plain')->withStatus(400)->withBody(Stream::create($message));
        }
    }
}