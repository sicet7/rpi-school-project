<?php

namespace App\Interfaces;

use Doctrine\ORM\EntityManagerInterface;

interface RepositoryInterface
{
    public function getEntityManager(): EntityManagerInterface;
    public function setEntityManager(EntityManagerInterface $entityManager): RepositoryInterface;
}