<?php

namespace App\Database;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Ramsey\Uuid\Uuid;

class UuidGenerator extends AbstractIdGenerator
{
    public function generate(EntityManager $em, $entity)
    {
        return Uuid::uuid4()->toString();
    }

    public function isPostInsertGenerator()
    {
        return false;
    }
}