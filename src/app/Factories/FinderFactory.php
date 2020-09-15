<?php

namespace App\Factories;

use App\Interfaces\FinderFactoryInterface;
use Symfony\Component\Finder\Finder;

class FinderFactory implements FinderFactoryInterface
{
    public function create(): Finder
    {
        return Finder::create();
    }
}