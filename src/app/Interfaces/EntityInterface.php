<?php

namespace App\Interfaces;

use Doctrine\ORM\Mapping\ClassMetadata;

interface EntityInterface
{
    public static function loadMetadata(ClassMetadata $metadata);
}