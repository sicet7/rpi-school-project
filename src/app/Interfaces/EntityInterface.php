<?php

declare(strict_types=1);

namespace App\Interfaces;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Interface EntityInterface
 * @package App\Interfaces
 */
interface EntityInterface
{
    /**
     * @param ClassMetadata $metadata
     * @return void
     */
    public static function loadMetadata(ClassMetadata $metadata): void;
}
