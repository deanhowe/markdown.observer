<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ComposerPackages
{
    public function getDependencies(bool $directOnly = false): Collection;

    public function getRequireString(bool $directOnly = false): string;

    public function getChecksum(): string;
}
