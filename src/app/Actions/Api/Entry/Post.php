<?php

declare(strict_types=1);

namespace App\Actions\Api\Entry;

use App\Database\Entities\Entry;
use App\Database\Repositories\EntryRepository;
use App\Interfaces\ActionInterface;
use App\Middleware\TokenMiddleware;
use App\Utility\BodyValidator;
use App\Utility\CurrentToken;
use App\Utility\Json;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Slim\Interfaces\RouteCollectorInterface;

/**
 * Class Post
 * @package App\Actions\Api\Entry
 */
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

    /**
     * Post constructor.
     * @param Json $json
     * @param CurrentToken $currentToken
     * @param EntryRepository $entryRepository
     */
    public function __construct(Json $json, CurrentToken $currentToken, EntryRepository $entryRepository)
    {
        $this->json = $json;
        $this->currentToken = $currentToken;
        $this->entryRepository = $entryRepository;
    }

    /**
     * @inheritDoc
     */
    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['POST'], '/api/entry', static::class)
            ->add(TokenMiddleware::class);
    }

    /**
     * @inheritDoc
     */
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
            $data = array_change_key_case($this->json->decode($bodyString), CASE_LOWER);
            BodyValidator::validatePost($data);

            $entry = new Entry($token);

            $entry->setSound((int)$data['sound']);
            $entry->setTemp($data['temp']);
            $entry->setLight($data['light']);
            $entry->setHumidity($data['humidity']);
            $entry->setCelsius($data['celsius']);
            $entry->setFahrenheit($data['fahrenheit']);
            $entry->setKelvin($data['kelvin']);

            $this->entryRepository->persist($entry);
            $this->entryRepository->flush();

            //FIXME: this is not ideal, but this was made quickly.
            return $response->withStatus(200)->withBody(Stream::create($this->json->encode([
                'id' => $entry->getId(),
                'sound' => $entry->getSound(),
                'temp' => $entry->getTemp(),
                'light' => $entry->getLight(),
                'humidity' => $entry->getHumidity(),
                'celsius' => $entry->getCelsius(),
                'fahrenheit' => $entry->getFahrenheit(),
                'kelvin' => $entry->getKelvin(),
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