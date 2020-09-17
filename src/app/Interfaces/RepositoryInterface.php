<?php

namespace App\Interfaces;

use Doctrine\ORM\EntityManagerInterface;

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
}
