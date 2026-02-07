<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Data;

class PackageIndexData extends Data
{
    public function __construct(
        #[ArrayType]
        public readonly array $packages,

        #[ArrayType]
        public readonly array $prodPackages,

        #[ArrayType]
        public readonly array $devPackages,

        #[ArrayType]
        public readonly array $topPackages
    ) {}

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'packages' => $this->packages,
            'prod_packages' => $this->prodPackages,
            'dev_packages' => $this->devPackages,
            'top_packages' => $this->topPackages,
        ];
    }
}
