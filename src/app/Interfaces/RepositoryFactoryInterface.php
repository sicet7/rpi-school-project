<?php

namespace App\Interfaces;

use DI\Factory\RequestedEntry;

/**
 * Interface RepositoryFactoryInterface
 * @package App\Interfaces
 */
interface RepositoryFactoryInterface
{
    /**
     * @param RequestedEntry $entry
     * @return RepositoryInterface
     */
    public function create(RequestedEntry $entry): RepositoryInterface;
}
