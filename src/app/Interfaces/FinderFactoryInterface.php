<?php

declare(strict_types=1);

namespace App\Interfaces;

use Symfony\Component\Finder\Finder;

/**
 * Interface FinderFactoryInterface
 * @package App\Interfaces
 */
interface FinderFactoryInterface
{
    /**
     * @return Finder
     */
    public function create(): Finder;
}
