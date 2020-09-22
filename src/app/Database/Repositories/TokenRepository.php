<?php

declare(strict_types=1);

namespace App\Database\Repositories;

use App\Database\Entities\Token;
use App\Interfaces\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Query\QueryException;

/**
 * Class TokenRepository
 * @package App\Database\Repositories
 */
class TokenRepository implements RepositoryInterface
{
    private const ENTITY = Token::class;

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
     * @param Token $token
     * @return TokenRepository
     */
    public function persist(Token $token): TokenRepository
    {
        $this->getEntityManager()->persist($token);
        return $this;
    }

    /**
     * @return TokenRepository
     */
    public function flush(): TokenRepository
    {
        $this->getEntityManager()->flush();
        return $this;
    }

    /**
     * @param string $id
     * @return Token|null
     * @throws QueryException
     */
    public function findById(string $id): ?Token
    {
        $data = $this->getList(Criteria::create()->where(Criteria::expr()->eq('id', $id)));
        if ($data->isEmpty()) {
            return null;
        }
        $first = $data->first();
        if ($first instanceof Token) {
            return $first;
        }
        return null;
    }

    /**
     * @param string $value
     * @return Token|null
     * @throws QueryException
     */
    public function findByValue(string $value): ?Token
    {
        $data = $this->getList(Criteria::create()->where(Criteria::expr()->eq('value', $value)));
        if ($data->isEmpty() || !($data->first() instanceof Token)) {
            return null;
        }
        return $data->first();
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
     * @return Token
     * @throws EntityNotFoundException|QueryException
     */
    public function getById(string $id): Token
    {
        $token = $this->findById($id);
        if ($token instanceof Token) {
            return $token;
        }
        throw new EntityNotFoundException(
            'Failed to find a token with the id: "' . $id . '".'
        );
    }

    /**
     * @param string $id
     * @return $this
     * @throws QueryException
     */
    public function deleteById(string $id): TokenRepository
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->delete(static::ENTITY, 'e');
        $queryBuilder->addCriteria(Criteria::create()->where(Criteria::expr()->eq('id', $id)));
        $queryBuilder->getQuery()->getResult();
        return $this;
    }

    /**
     * @param Token $token
     * @return TokenRepository
     */
    public function refresh(Token $token): TokenRepository
    {
        $this->getEntityManager()->refresh($token);
        return $this;
    }
}
