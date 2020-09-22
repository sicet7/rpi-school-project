<?php

declare(strict_types=1);

namespace App\Database\Repositories;

use App\Database\Entities\Entry;
use App\Database\Entities\Token;
use App\Interfaces\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Ramsey\Uuid\UuidInterface;

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
     * @inheritDoc
     */
    public function getPersister(): EntityPersister
    {
        return $this->getEntityManager()->getUnitOfWork()->getEntityPersister(static::ENTITY);
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
     * @param Entry $entry
     * @return EntryRepository
     */
    public function save(Entry $entry): EntryRepository
    {
        $this->persist($entry);
        $this->flush();
        return $this;
    }

    /**
     * @param $id
     * @param bool $includeDeleted
     * @return Entry|null
     */
    public function findById($id, bool $includeDeleted = false): ?Entry
    {
        $id = $this->getId($id);
        $data = $this->findBy($this->getCriteria($id, $includeDeleted));
        $first = $data->first();
        if ($first instanceof Entry) {
            return $first;
        }
        return null;
    }

    /**
     * @param Criteria $criteria
     * @return ArrayCollection
     */
    public function findBy(Criteria $criteria): ArrayCollection
    {
        $data = $this->getPersister()->loadCriteria($criteria);
        return new ArrayCollection($data);
    }

    /**
     * @param $id
     * @param bool $includeDeleted
     * @return Entry
     * @throws EntityNotFoundException
     */
    public function getById($id, bool $includeDeleted = false): Entry
    {
        $entry = $this->findById($id, $includeDeleted);
        if ($entry instanceof Entry) {
            return $entry;
        }
        throw new EntityNotFoundException(
            'Failed to find a entry with the id: "' . $id . '".'
        );
    }

    /**
     * @param Entry $entry
     * @param bool $hardDelete
     * @return EntryRepository
     */
    public function delete(Entry $entry, bool $hardDelete = false): EntryRepository
    {
        if ($hardDelete) {
            $this->getEntityManager()->remove($entry);
        } else {
            $entry->setDeletedAt();
            $this->save($entry);
        }
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
     * @param Entry $entry
     * @param bool $includeDeleted
     * @return bool
     */
    public function exists(Entry $entry, bool $includeDeleted = false): bool
    {
        return $this->getPersister()->exists($entry, $this->getCriteria($entry->getId(), $includeDeleted));
    }

    /**
     * @param mixed $id
     * @param bool $includeDeleted
     * @return Criteria
     */
    private function getCriteria($id, bool $includeDeleted): Criteria
    {
        $criteria = Criteria::create();
        $builder = Criteria::expr();
        $criteria->where($builder->eq('id', $id));
        if (!$includeDeleted) {
            $criteria->andWhere($builder->isNull('deleted_at'));
        }
        return $criteria;
    }

    /**
     * @param string|UuidInterface $id
     * @return string
     */
    private function getId($id): string
    {
        if ($id instanceof UuidInterface) {
            return $id->toString();
        }
        return $id;
    }
}
