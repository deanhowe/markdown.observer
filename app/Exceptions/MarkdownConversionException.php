<?php

namespace App\Exceptions;

class MarkdownConversionException extends BaseException
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param string|null $markdownContent
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = 'Failed to convert Markdown content',
        protected ?string $markdownContent = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 500, $previous);
    }

    /**
     * Get the error code for this exception
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'MARKDOWN_CONVERSION_FAILED';
    }

    /**
     * Get the error type for this exception
     *
     * @return string
     */
    public function getErrorType(): string
    {
        return 'Processing Error';
    }

    /**
     * Get additional data for this exception
     *
     * @return array
     */
    public function getAdditionalData(): array
    {
        $data = [];

        if ($this->markdownContent) {
            // Only include a preview of the content to avoid huge error messages
            $preview = substr($this->markdownContent, 0, 100);
            if (strlen($this->markdownContent) > 100) {
                $preview .= '...';
            }
            $data['content_preview'] = $preview;
        }

        return $data;
    }
}
