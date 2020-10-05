<?php

declare(strict_types=1);

namespace App\Actions\Api\Entry;

use App\Database\Entities\Entry;
use App\Database\Repositories\EntryRepository;
use App\DTO\EntryAverage;
use App\Interfaces\ActionInterface;
use App\Utility\Json;
use Doctrine\Common\Collections\Criteria;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Interfaces\RouteCollectorInterface;

class Average implements ActionInterface
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
     * Average constructor.
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
        $routeCollector->map(['GET'], '/api/entryAverage', static::class);
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

            $entries = $this->entryRepository->getAverage($criteria);

            $returnArray = [];

            foreach ($entries->toArray() as $entry) {
                /** @var EntryAverage $entry */
                if ($entry instanceof EntryAverage) {
                    $returnArray[] = $entry->toArray();
                }
            }

            return $response->withStatus(200)->withBody(Stream::create(
                $this->json->encode([
                    'skip' => $criteria->getFirstResult(),
                    'take' => $criteria->getMaxResults(),
                    'total' => $this->entryRepository->getAverageCount(),
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
}