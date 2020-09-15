<?php

namespace App\Interfaces;

use DI\Factory\RequestedEntry;

interface ActionFactoryInterface
{
    public function create(RequestedEntry $entry): ActionInterface;
}