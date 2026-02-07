<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection getDependencies(bool $directOnly = false)
 * @method static string getRequireString(bool $directOnly = false)
 *
 * @see \App\Services\ComposerPackagesService
 */
class ComposerPackages extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'composer.packages';
    }
}
