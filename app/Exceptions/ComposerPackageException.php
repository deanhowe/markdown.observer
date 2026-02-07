<?php

namespace App\Exceptions;

class ComposerPackageException extends \RuntimeException
{
    public static function processFailure(string $error): self
    {
        return new self("Composer process failed: {$error}");
    }

    public static function invalidJson(string $path): self
    {
        return new self("Invalid composer.json at: {$path}");
    }
}