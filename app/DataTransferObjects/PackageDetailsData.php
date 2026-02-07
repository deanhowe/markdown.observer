<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Data;

class PackageDetailsData extends Data
{
    public function __construct(
        #[ArrayType]
        public readonly array $package,

        #[ArrayType]
        public readonly array $markdownFiles
    ) {}

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'package' => $this->package,
            'markdown_files' => $this->markdownFiles,
        ];
    }
}
