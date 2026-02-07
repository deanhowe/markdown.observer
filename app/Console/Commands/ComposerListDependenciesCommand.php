<?php

namespace App\Console\Commands;

use App\Services\ComposerPackagesService;
use Illuminate\Console\Command as CommandBase;
use Symfony\Component\Console\Command\Command;

class ComposerListDependenciesCommand extends CommandBase
{
    /**
     * The name and signature of the console command.
     * composer show -f json | jq -r '.installed[] | "\(.name):\(.version)"' | paste -sd " " -
     *
     * @var string
     */
    protected $signature = 'composer:deps {--format=list : Output format (list|require)}';
    protected $description = 'List all dependencies with their versions';

    public function __construct(
        private readonly ComposerPackagesService $dependencyService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            if ($this->option('format') === 'require') {
                $output = "composer require " . $this->dependencyService->getRequireString();
            } else {
                $output = $this->dependencyService->getDependencies()
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
