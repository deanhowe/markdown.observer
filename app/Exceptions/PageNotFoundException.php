<?php

namespace App\Exceptions;

class PageNotFoundException extends BaseException
{
    /**
     * Create a new exception instance.
     *
     * @param string $filename
     * @param \Throwable|null $previous
     */
    public function __construct(
        protected string $filename,
        ?\Throwable $previous = null
    ) {
        parent::__construct("Page not found: {$filename}", 404, $previous);
    }

    /**
     * Get the error code for this exception
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'PAGE_NOT_FOUND';
    }

    /**
     * Get the error type for this exception
     *
     * @return string
     */
    public function getErrorType(): string
    {
        return 'Not Found';
    }

    /**
     * Get additional data for this exception
     *
     * @return array
     */
    public function getAdditionalData(): array
    {
        return [
            'filename' => $this->filename,
        ];
    }
}
