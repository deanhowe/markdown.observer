<?php

namespace App\Contracts;

interface ComposerPackageRepository
{
    public function getDependencies(bool $directOnly = false): Collection;
    public function getRequireString(bool $directOnly = false): string;
    public function getCachedDependencies(): Collection;
}