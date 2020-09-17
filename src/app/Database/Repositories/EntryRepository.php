<?php

declare(strict_types=1);

namespace App\Database\Repositories;

use App\Interfaces\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EntryRepository
 * @package App\Database\Repositories
 */
class EntryRepository implements RepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return RepositoryInterface
     */
    public function setEntityManager(EntityManagerInterface $entityManager): RepositoryInterface
    {
        $this->entityManager = $entityManager;
        return $this;
    }
}
