<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class HtmlToMarkdownData extends Data
{
    public function __construct(
        #[StringType]
        public readonly string $markdown
    ) {}

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'markdown' => $this->markdown,
        ];
    }
}
