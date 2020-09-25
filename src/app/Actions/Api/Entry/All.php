<?php

declare(strict_types=1);

namespace App\Actions\Api\Entry;

use App\Database\Entities\Entry;
use App\Database\Repositories\EntryRepository;
use App\Interfaces\ActionInterface;
use App\Utility\Json;
use Doctrine\Common\Collections\Criteria;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Interfaces\RouteCollectorInterface;

class All implements ActionInterface
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

    public static function register(RouteCollectorInterface $routeCollector): void
    {
        $routeCollector->map(['GET'], '/api/entry', static::class);
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $output = [];
            parse_str($request->getUri()->getQuery(), $output);
            $output = array_change_key_case($output, CASE_LOWER);

            $criteria = Criteria::create();

            if (isset($output['skip']) && is_numeric($output['skip'])) {
                $criteria->setFirstResult($output['skip']);
            } else {
                $criteria->setFirstResult(0);
            }

            if (isset($output['take']) && is_numeric($output['take']) && $output['take'] <= 500) {
                $criteria->setMaxResults($output['take']);
            } else {
                $criteria->setMaxResults(20);
            }

            if (isset($output['orderby']) && property_exists(Entry::class, $output['orderby'])) {
                if (isset($output['direction']) &&
                    is_string($output['direction']) &&
                    strtolower($output['direction']) == 'desc'
                ) {
                    $criteria->orderBy([$output['orderby'] => Criteria::DESC]);
                } else {
                    $criteria->orderBy([$output['orderby'] => Criteria::ASC]);
                }
            }

            $entries = $this->entryRepository->getList($criteria);

            $returnArray = [];

            foreach ($entries as $entry) {
                /** @var Entry $entry */
                $returnArray[] = $this->entryToArray($entry);
            }

            //FIXME: this is not ideal, but this was made quickly.
            return $response->withStatus(200)->withBody(Stream::create(
                $this->json->encode([
                    'skip' => $criteria->getFirstResult(),
                    'take' => $criteria->getMaxResults(),
                    'total' => $this->entryRepository->countList(),
                    'data' => $returnArray,
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

    /**
     * @param Entry $entry
     * @return array
     */
    private function entryToArray(Entry $entry): array
    {
        return [
            'id' => $entry->getId(),
            'sound' => $entry->getSound(true),
            'temp' => $entry->getTemp(true),
            'light' => $entry->getLight(true),
            'humidity' => $entry->getHumidity(),
            'celsius' => $entry->getCelsius(),
            'fahrenheit' => $entry->getFahrenheit(),
            'kelvin' => $entry->getKelvin(),
            'token' => $entry->getToken()->getId(),
            'created_at' => $entry->getCreatedAt(true),
            'updated_at' => $entry->getUpdatedAt(true),
        ];
    }
}