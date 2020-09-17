<?php

namespace App\Database;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Ramsey\Uuid\Uuid;

/**
 * Class UuidGenerator
 * @package App\Database
 */
class UuidGenerator extends AbstractIdGenerator
{
    /**
     * @param EntityManager $em
     * @param object|null $entity
     * @return mixed|string
     */
    public function generate(EntityManager $em, $entity)
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * @return bool
     */
    public function isPostInsertGenerator()
    {
        return false;
    }
}
