<?php

declare(strict_types=1);

namespace App\Database\Repositories;

use App\Database\Entities\Entry;
use App\DTO\EntryAverage;
use App\Interfaces\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\Query\QueryException;

/**
 * Class EntryRepository
 * @package App\Database\Repositories
 */
class EntryRepository implements RepositoryInterface
{
    private const ENTITY = Entry::class;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @inheritDoc
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @inheritDoc
     */
    public function setEntityManager(EntityManagerInterface $entityManager): RepositoryInterface
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @param Entry $entry
     * @return EntryRepository
     */
    public function persist(Entry $entry): EntryRepository
    {
        $this->getEntityManager()->persist($entry);
        return $this;
    }

    /**
     * @return EntryRepository
     */
    public function flush(): EntryRepository
    {
        $this->getEntityManager()->flush();
        return $this;
    }

    /**
     * @param string $id
     * @return Entry|null
     * @throws QueryException
     */
    public function findById(string $id): ?Entry
    {
        $data = $this->getList(Criteria::create()->where(Criteria::expr()->eq('id', $id)));
        $first = $data->first();
        if ($first instanceof Entry) {
            return $first;
        }
        return null;
    }

    /**
     * @param Criteria|null $criteria
     * @return ArrayCollection
     * @throws QueryException
     */
    public function getList(?Criteria $criteria = null): ArrayCollection
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('e');
        $queryBuilder->from(static::ENTITY, 'e');
        if ($criteria instanceof Criteria) {
            $queryBuilder->addCriteria($criteria);
        }
        $data = $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
        return new ArrayCollection($data);
    }

    /**
     * @param string $id
     * @return Entry
     * @throws EntityNotFoundException
     * @throws QueryException
     */
    public function getById(string $id): Entry
    {
        $entry = $this->findById($id);
        if ($entry instanceof Entry) {
            return $entry;
        }
        throw new EntityNotFoundException(
            'Failed to find a entry with the id: "' . $id . '".'
        );
    }

    /**
     * @param string $id
     * @return $this
     * @throws QueryException
     */
    public function deleteById(string $id): EntryRepository
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->delete(static::ENTITY, 'e');
        $queryBuilder->addCriteria(Criteria::create()->where(Criteria::expr()->eq('id', $id)));
        $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
        return $this;
    }

    /**
     * @param Entry $entry
     * @return EntryRepository
     */
    public function refresh(Entry $entry): EntryRepository
    {
        $this->getEntityManager()->refresh($entry);
        return $this;
    }

    /**
     * @param Criteria|null $criteria
     * @return int
     * @throws QueryException
     * @throws NonUniqueResultException
     */
    public function countList(?Criteria $criteria = null): int
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('COUNT(e)');
        $queryBuilder->from(static::ENTITY, 'e');
        if ($criteria instanceof Criteria) {
            $queryBuilder->addCriteria($criteria);
        }
        $count = $queryBuilder->getQuery()->getOneOrNullResult();
        if (is_array($count) && !empty($count)) {
            $count = $count[array_keys($count)[0]];
        }
        if ($count !== null && is_numeric($count)) {
            return (int) $count;
        }
        return 0;
    }

    /**
     * @param Criteria|null $criteria
     * @return ArrayCollection
     * @throws QueryException
     */
    public function getAverage(?Criteria $criteria = null): ArrayCollection
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select(sprintf(
            "NEW %s(COUNT(e), ROUND(AVG(e.sound)), AVG(e.temp), AVG(e.light), " .
            "AVG(e.humidity), AVG(e.celsius), AVG(e.fahrenheit), AVG(e.kelvin), " .
            "DATE(e.created_at)) as dto, DATE(e.created_at) as qDate",
            EntryAverage::class
        ));
        $queryBuilder->from(static::ENTITY, 'e');
        $queryBuilder->groupBy('qDate');
        $queryBuilder->orderBy(new OrderBy('DATE(e.created_at)', 'DESC'));
        if ($criteria instanceof Criteria) {
            $queryBuilder->addCriteria($criteria);
        }
        $data = $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
        $rData = [];
        foreach ($data as $e) {
            if (isset($e['dto']) && $e['dto'] instanceof EntryAverage) {
                $rData[] = $e['dto'];
            }
        }
        return new ArrayCollection($rData);
    }

    /**
     * @param Criteria|null $criteria
     * @return int
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    public function getAverageCount(?Criteria $criteria = null): int
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('COUNT(DISTINCT DATE(e.created_at)), DATE(e.created_at) as qDate');
        $queryBuilder->from(static::ENTITY, 'e');
        $queryBuilder->groupBy('qDate');
        if ($criteria instanceof Criteria) {
            $queryBuilder->addCriteria($criteria);
        }
        $count = $queryBuilder->getQuery()->getOneOrNullResult();
        if (is_array($count) && !empty($count)) {
            $count = $count[array_keys($count)[0]];
        }
        if ($count !== null && is_numeric($count)) {
            return (int) $count;
        }
        return 0;
    }
}
