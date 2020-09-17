<?php

declare(strict_types=1);

namespace App\Factories;

use App\Interfaces\FinderFactoryInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class FinderFactory
 * @package App\Factories
 */
class FinderFactory implements FinderFactoryInterface
{
    /**
     * @return Finder
     */
    public function create(): Finder
    {
        return Finder::create();
    }
}
