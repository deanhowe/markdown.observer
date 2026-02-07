<?php

namespace App\DataTransferObjects;

use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ComposerPackageData extends Data
{
    public function __construct(
        #[Required, StringType]
        public readonly string $name,

        #[Required, StringType]
        public readonly string $version,

        #[StringType]
        public readonly string $description = '',

        #[Nullable, StringType]
        public readonly ?string $homepage = null,

        #[Nullable]
        public readonly ?bool $directDependency = null,

        #[Nullable, StringType]
        public readonly ?string $source = null,

        #[Nullable]
        public readonly ?bool $abandoned = null,

        #[ArrayType]
        public readonly array $dependencies = [],

        #[BooleanType]
        public readonly bool $isDev = false,

        #[Nullable, ArrayType]
        public readonly ?array $logo = null,

        #[Nullable, StringType]
        public readonly ?string $readmeHtml = null,

        #[Nullable, ArrayType]
        public readonly ?array $markdownDirectoryTree = null,

        #[Nullable, StringType]
        public readonly ?string $repository = null,

        #[Nullable, StringType]
        public readonly ?string $latestVersion = null,

        #[Nullable, BooleanType]
        public readonly ?bool $hasNewerVersion = null,

        #[Nullable, ArrayType]
        public readonly ?array $maintainers = null,

        #[Nullable, ArrayType]
        public readonly ?array $downloads = null,

        #[Nullable]
        public readonly ?int $rank = null,

        #[Nullable, StringType]
        public readonly ?string $type = null,
    ) {}

    /**
     * Create a new instance from an array, with validation
     */
    public static function fromArrayWithValidation(array $data): static
    {
        // Normalize data keys and values
        $normalizedData = self::normalizeData($data);

        try {
            // Create the object directly instead of using self::from($data)
            // This avoids the infinite loop in CreationContextFactory
            return new self(
                name: $normalizedData['name'],
                version: $normalizedData['version'],
                description: $normalizedData['description'],
                homepage: $normalizedData['homepage'],
                directDependency: $normalizedData['directDependency'],
                source: $normalizedData['source'],
                abandoned: $normalizedData['abandoned'],
                dependencies: $normalizedData['dependencies'],
                isDev: $normalizedData['isDev'],
                logo: $normalizedData['logo'],
                readmeHtml: $normalizedData['readmeHtml'],
                markdownDirectoryTree: $normalizedData['markdownDirectoryTree'],
                repository: $normalizedData['repository'] ?? null,
                latestVersion: $normalizedData['latestVersion'] ?? null,
                hasNewerVersion: $normalizedData['hasNewerVersion'] ?? null,
                maintainers: $normalizedData['maintainers'] ?? null,
                downloads: $normalizedData['downloads'] ?? null,
                rank: $normalizedData['rank'] ?? null,
                type: $normalizedData['type'] ?? null,
            );
        } catch (ValidationException $e) {
            // Log the validation error
            logger()->warning('Validation failed for ComposerPackageData', [
                'data' => $data,
                'errors' => $e->errors(),
            ]);

            // Create a minimal valid object with the normalized data
            return new self(
                name: $normalizedData['name'],
                version: $normalizedData['version'],
                description: $normalizedData['description'],
                homepage: $normalizedData['homepage'],
                directDependency: $normalizedData['directDependency'],
                source: $normalizedData['source'],
                abandoned: $normalizedData['abandoned'],
                dependencies: $normalizedData['dependencies'],
                isDev: $normalizedData['isDev'],
                logo: $normalizedData['logo'],
                readmeHtml: $normalizedData['readmeHtml'],
                markdownDirectoryTree: $normalizedData['markdownDirectoryTree'],
                repository: $normalizedData['repository'] ?? null,
                latestVersion: $normalizedData['latestVersion'] ?? null,
                hasNewerVersion: $normalizedData['hasNewerVersion'] ?? null,
                maintainers: $normalizedData['maintainers'] ?? null,
                downloads: $normalizedData['downloads'] ?? null,
                rank: $normalizedData['rank'] ?? null,
                type: $normalizedData['type'] ?? null,
            );
        }
    }

    /**
     * Normalize data for creating a DTO
     */
    private static function normalizeData(array $data): array
    {
        $normalized = [];

        // Convert string boolean values to actual booleans
        if (isset($data['direct-dependency']) && is_string($data['direct-dependency'])) {
            $normalized['directDependency'] = filter_var($data['direct-dependency'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        } elseif (isset($data['direct-dependency'])) {
            $normalized['directDependency'] = $data['direct-dependency'];
        } else {
            $normalized['directDependency'] = $data['directDependency'] ?? null;
        }

        if (isset($data['abandoned']) && is_string($data['abandoned'])) {
            $normalized['abandoned'] = filter_var($data['abandoned'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        } else {
            $normalized['abandoned'] = $data['abandoned'] ?? null;
        }

        // Set all other fields with defaults
        $normalized['name'] = $data['name'] ?? 'unknown';
        $normalized['version'] = $data['version'] ?? 'unknown';
        $normalized['description'] = $data['description'] ?? '';
        $normalized['homepage'] = $data['homepage'] ?? null;
        $normalized['source'] = $data['source'] ?? null;
        $normalized['dependencies'] = $data['dependencies'] ?? [];
        $normalized['isDev'] = $data['isDev'] ?? false;
        $normalized['logo'] = $data['logo'] ?? null;
        $normalized['readmeHtml'] = $data['readmeHtml'] ?? null;
        $normalized['markdownDirectoryTree'] = $data['markdown_directory_tree'] ?? $data['markdownDirectoryTree'] ?? null;
        $normalized['rank'] = $data['rank'] ?? null;
        $normalized['type'] = $data['type'] ?? (($data['isDev'] ?? false) ? 'dev' : 'prod');

        return $normalized;
    }

    /**
     * Convert the object to an array.
     */
    public function toArray(): array
    {
        $type = $this->type ?? ($this->isDev ? 'dev' : 'prod');

        return [
            'name' => $this->name,
            'version' => $this->version,
            'description' => $this->description,
            'homepage' => $this->homepage,
            'direct_dependency' => $this->directDependency,
            'source' => $this->source,
            'abandoned' => $this->abandoned,
            'dependencies' => $this->dependencies,
            'is_dev' => $this->isDev,
            'logo' => $this->logo,
            'readme_html' => $this->readmeHtml,
            'markdown_directory_tree' => $this->markdownDirectoryTree,
            // Additional properties for the frontend
            'rank' => $this->rank ?? null,
            'type' => $type,
        ];
    }
}
