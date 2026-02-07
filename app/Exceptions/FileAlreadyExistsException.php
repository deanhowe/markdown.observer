<?php

namespace App\Exceptions;

class FileAlreadyExistsException extends BaseException
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
        parent::__construct("File already exists: {$filename}", 409, $previous);
    }

    /**
     * Get the error code for this exception
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'FILE_ALREADY_EXISTS';
    }

    /**
     * Get the error type for this exception
     *
     * @return string
     */
    public function getErrorType(): string
    {
        return 'Conflict';
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
