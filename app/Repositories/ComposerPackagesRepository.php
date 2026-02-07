<?php

namespace App\Repositories;

use App\Contracts\ComposerPackages;
use App\DataTransferObjects\ComposerPackageData;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;

class ComposerPackagesRepository implements ComposerPackages
{
    protected string $composerJsonPath;

    protected ?string $checksum = null;

    public function __construct()
    {
        $this->composerJsonPath = base_path('composer.json');
        $this->refreshChecksum();
    }

    /**
     * Get all dependencies
     *
     * @param  bool  $directOnly  Whether to get only direct dependencies
     *
     * @throws \RuntimeException
     */
    public function getDependencies(bool $directOnly = false): Collection
    {
        $command = ['composer', 'show'];
        if ($directOnly) {
            $command[] = '--direct';
        }
        $command[] = '-f';
        $command[] = 'json';

        $process = new Process($command);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $json = json_decode($process->getOutput(), true);

        return collect($json['installed'])->map(function (array $package): ComposerPackageData {
            try {
                return ComposerPackageData::fromArrayWithValidation($package);
            } catch (\Exception $e) {
                // Log the error and return a minimal valid object
                logger()->error('Failed to create ComposerPackageData', [
                    'package' => $package,
                    'error' => $e->getMessage(),
                ]);

                return new ComposerPackageData(
                    name: $package['name'] ?? 'unknown',
                    version: $package['version'] ?? 'unknown'
                );
            }
        });
    }

    /**
     * Get dependencies formatted for composer require command
     *
     * @param  bool  $directOnly  Whether to get only direct dependencies
     *
     * @throws \RuntimeException
     */
    public function getRequireString(bool $directOnly = false): string
    {
        return $this->getDependencies($directOnly)
            ->map(fn (ComposerPackageData $package) => "{$package->name}:{$package->version}")
            ->implode(' ');
    }

    protected function refreshChecksum(): void
    {
        $this->checksum = md5_file($this->composerJsonPath);
    }

    public function setComposerJsonPath(string $path): self
    {
        $this->composerJsonPath = $path;
        $this->refreshChecksum();

        return $this;
    }

    public function setChecksum(string $checksum): self
    {
        $this->checksum = $checksum;

        return $this;
    }

    public function getChecksum(): string
    {
        return $this->checksum;
    }
}
