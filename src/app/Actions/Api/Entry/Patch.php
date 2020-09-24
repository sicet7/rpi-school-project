<?php

declare(strict_types=1);

namespace App\Actions\Api\Entry;

use App\Database\Repositories\EntryRepository;
use App\Interfaces\ActionInterface;
use App\Middleware\TokenMiddleware;
use App\Utility\BodyValidator;
use App\Utility\Json;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Slim\Interfaces\RouteCollectorInterface;

class Patch implements ActionInterface
{

    /**
     * @var EntryRepository
     */
    private EntryRepository $entryRepository;
    /**
     * @var Json
     */
    private Json $json;

    /**
     * Patch constructor.
     * @param EntryRepository $entryRepository
     * @param Json $json
     */
    public function __construct(EntryRepository $entryRepository, Json $json)
    {
        $this->entryRepository = $entryRepository;
        $this->json = $json;
    }

    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['PATCH'], '/api/entry/{id}', static::class)
            ->add(TokenMiddleware::class);
    }

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

            $request->getBody()->rewind();
            $bodyString = $request->getBody()->getContents();

            Validator::json()->check($bodyString);
            $data = array_change_key_case($this->json->decode($bodyString), CASE_LOWER);
            BodyValidator::validatePatch($data);

            if (isset($data['sound'])) {
                $entry->setSound($data['sound']);
            }

            if (isset($data['temp'])) {
                $entry->setTemp($data['temp']);
            }

            if (isset($data['light'])) {
                $entry->setLight($data['light']);
            }

            if (isset($data['humidity'])) {
                $entry->setHumidity($data['humidity']);
            }

            if (isset($data['celsius'])) {
                $entry->setCelsius($data['celsius']);
            }

            if (isset($data['fahrenheit'])) {
                $entry->setFahrenheit($data['fahrenheit']);
            }

            if (isset($data['kelvin'])) {
                $entry->setKelvin($data['kelvin']);
            }

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