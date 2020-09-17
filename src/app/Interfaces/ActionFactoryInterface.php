<?php

namespace App\Interfaces;

use DI\Factory\RequestedEntry;

/**
 * Interface ActionFactoryInterface
 * @package App\Interfaces
 */
interface ActionFactoryInterface
{
    /**
     * @param RequestedEntry $entry
     * @return ActionInterface
     */
    public function create(RequestedEntry $entry): ActionInterface;
}
