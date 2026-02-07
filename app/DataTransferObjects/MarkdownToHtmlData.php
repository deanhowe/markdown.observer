<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class MarkdownToHtmlData extends Data
{
    public function __construct(
        #[StringType]
        public readonly string $html
    ) {}

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'html' => $this->html,
        ];
    }
}
