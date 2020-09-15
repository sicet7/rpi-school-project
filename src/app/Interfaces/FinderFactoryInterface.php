<?php

namespace App\Interfaces;

use Symfony\Component\Finder\Finder;

interface FinderFactoryInterface
{
    public function create(): Finder;
}