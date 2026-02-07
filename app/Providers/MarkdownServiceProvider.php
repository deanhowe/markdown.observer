<?php

namespace App\Providers;

use App\Services\MarkdownService;
use Illuminate\Support\ServiceProvider;
use League\HTMLToMarkdown\HtmlConverter;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class MarkdownServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(HtmlConverter::class, function ($app) {
            return new HtmlConverter([
                'header_style' => 'atx' // Use # style headers instead of underlines
            ]);
        });

        $this->app->singleton(MarkdownService::class, function ($app) {
            return new MarkdownService(
                $app->make(MarkdownRenderer::class),
                $app->make(HtmlConverter::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
