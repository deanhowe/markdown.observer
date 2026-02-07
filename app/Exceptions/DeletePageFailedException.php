<?php

namespace App\Exceptions;

class DeletePageFailedException extends BaseException
{
    /**
     * Create a new exception instance.
     *
     * @param string $filename
     * @param string|null $reason
     * @param \Throwable|null $previous
     */
    public function __construct(
        protected string $filename,
        protected ?string $reason = null,
        ?\Throwable $previous = null
    ) {
        $message = "Failed to delete page: {$filename}";
        if ($reason) {
            $message .= " - {$reason}";
        }
        parent::__construct($message, 500, $previous);
    }

    /**
     * Get the error code for this exception
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'DELETE_PAGE_FAILED';
    }

    /**
     * Get the error type for this exception
     *
     * @return string
     */
    public function getErrorType(): string
    {
        return 'Internal Server Error';
    }

    /**
     * Get additional data for this exception
     *
     * @return array
     */
    public function getAdditionalData(): array
    {
        $data = [
            'filename' => $this->filename,
        ];

        if ($this->reason) {
            $data['reason'] = $this->reason;
        }

        return $data;
    }
}
