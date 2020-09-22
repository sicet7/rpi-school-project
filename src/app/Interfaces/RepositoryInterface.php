<?php

declare(strict_types=1);

namespace App\Interfaces;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Persisters\Entity\EntityPersister;

/**
 * Interface RepositoryInterface
 * @package App\Interfaces
 */
interface RepositoryInterface
{
    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface;

    /**
     * @param EntityManagerInterface $entityManager
     * @return RepositoryInterface
     */
    public function setEntityManager(EntityManagerInterface $entityManager): RepositoryInterface;

    /**
     * @return EntityPersister
     */
    public function getPersister(): EntityPersister;
}
