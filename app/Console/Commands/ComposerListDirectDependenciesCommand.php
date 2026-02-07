<?php

namespace App\Console\Commands;

use App\Services\ComposerPackagesService;
use Illuminate\Console\Command;

class ComposerListDirectDependenciesCommand extends Command
{
    protected $signature = 'composer:deps:direct {--format=list : Output format (list|require)}';
    protected $description = 'List direct dependencies with their versions';

    public function __construct(
        private readonly ComposerPackagesService $dependencyService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            if ($this->option('format') === 'require') {
                $output = "composer require " . $this->dependencyService->getRequireString(directOnly: true);
            } else {
                $output = $this->dependencyService->getDependencies(directOnly: true)
                    ->map(fn ($package) => "{$package->name} {$package->version} {$package->description}")
                    ->implode(PHP_EOL);
            }

            $this->info($output);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
