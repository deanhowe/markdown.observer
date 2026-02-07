<?php

namespace App\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    /**
     * Get the error code for this exception
     *
     * @return string
     */
    abstract public function getErrorCode(): string;

    /**
     * Get the error type for this exception
     *
     * @return string
     */
    abstract public function getErrorType(): string;

    /**
     * Get additional data for this exception
     *
     * @return array
     */
    public function getAdditionalData(): array
    {
        return [];
    }

    /**
     * Convert the exception to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'error' => [
                'code' => $this->getErrorCode(),
                'type' => $this->getErrorType(),
                'message' => $this->getMessage(),
                'data' => $this->getAdditionalData(),
            ],
        ];
    }
}
