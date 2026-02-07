<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log all exceptions
            $context = [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];

            // Log to the exceptions channel
            Log::channel('exceptions')->error('Exception occurred', $context);

            // Also log to the appropriate specialized channel if applicable
            if ($e instanceof MarkdownConversionException) {
                Log::channel('markdown')->error('Markdown conversion failed', $context);
            }
        });

        // Handle API exceptions
        $this->renderable(function (BaseException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return new JsonResponse(
                    $e->toArray(),
                    $e->getCode() >= 100 && $e->getCode() < 600 ? $e->getCode() : 500
                );
            }
        });
    }
}
