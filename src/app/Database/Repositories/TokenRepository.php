<?php

declare(strict_types=1);

namespace App\Database\Repositories;

use App\Database\Entities\Token;
use App\Interfaces\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Persisters\Entity\EntityPersister;

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
     * @inheritDoc
     */
    public function getPersister(): EntityPersister
    {
        return $this->getEntityManager()->getUnitOfWork()->getEntityPersister(static::ENTITY);
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
     * @param Token $token
     * @return TokenRepository
     */
    public function save(Token $token): TokenRepository
    {
        $this->persist($token);
        $this->flush();
        return $this;
    }

    /**
     * @param $id
     * @param bool $includeDeleted
     * @return Token|null
     */
    public function findById($id, bool $includeDeleted = false): ?Token
    {
        if ($id instanceof UuidInterface) {
            $id = $id->toString();
        }
        $data = $this->getPersister()->loadCriteria($this->getCriteria($id, $includeDeleted));
        if (empty($data)) {
            return null;
        }
        $first = $data[array_keys($data)[0]];
        if ($first instanceof Token) {
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
     * @return Token
     * @throws EntityNotFoundException
     */
    public function getById($id, bool $includeDeleted = false): Token
    {
        $token = $this->findById($id, $includeDeleted);
        if ($token instanceof Token) {
            return $token;
        }
        throw new EntityNotFoundException(
            'Failed to find a token with the id: "' . $id . '".'
        );
    }

    /**
     * @param Token $token
     * @param bool $hardDelete
     * @return TokenRepository
     */
    public function delete(Token $token, bool $hardDelete = false): TokenRepository
    {
        if ($hardDelete) {
            $this->getEntityManager()->remove($token);
        } else {
            $token->setDeletedAt();
            $this->save($token);
        }
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

    /**
     * @param Token $token
     * @param bool $includeDeleted
     * @return bool
     */
    public function exists(Token $token, bool $includeDeleted = false): bool
    {
        return $this->getPersister()->exists($token, $this->getCriteria($token->getId(), $includeDeleted));
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
}
