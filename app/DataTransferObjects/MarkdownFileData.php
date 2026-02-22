<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

class MarkdownFileData extends Data
{
    public function __construct(
        #[ArrayType]
        public readonly array $package,

        #[StringType]
        public readonly string $filePath,

        #[StringType]
        public readonly string $content,

        #[StringType]
        public readonly string $html,

        #[Nullable, StringType]
        public readonly ?string $phpStormUrl,

        #[Nullable, StringType]
        public readonly ?string $relativePath,

        public readonly ?int $lastModified = null
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
            'file_path' => $this->filePath,
            'content' => $this->content,
            'html' => $this->html,
            'phpstorm_url' => $this->phpStormUrl,
            'relative_path' => $this->relativePath,
            'last_modified' => $this->lastModified,
        ];
    }
}
