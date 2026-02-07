# ComposerPackageData README HTML Integration

## Overview

This document describes the changes made to the `ComposerPackageData` DTO and `ComposerPackagesService` to include README.md content converted to HTML in the response. This enhancement allows for displaying formatted README content in the carousel on the homepage.

## Changes Made

1. Extended the `ComposerPackageData` DTO to include a README HTML field:
   - Added a nullable string field `readmeHtml` to store the HTML content of the README.md file
   - Updated the `fromArrayWithValidation` method to handle the new field
   - Updated the `toArray` method to include the new field in the returned array as `readme_html`

2. Enhanced the `ComposerPackagesService` to handle README HTML conversion:
   - Added the `MarkdownService` as a dependency to convert markdown to HTML
   - Implemented a `getPackageReadme` method to retrieve README.md content and optionally convert it to HTML
   - Updated the `analyze` method to accept a parameter that indicates whether to include README HTML content
   - Updated the `getCached` method to support the `includeReadmeHtml` parameter
   - Updated the `invalidateCache` method to clear both cache versions

3. Added tests for the new functionality:
   - Test for the `getPackageReadme` method with HTML conversion
   - Test for the `analyze` method with `includeReadmeHtml=true`

## Benefits

- **Rich Content**: Package data now includes formatted README content, enhancing the UI experience
- **Flexible API**: The API now allows clients to request README HTML content only when needed
- **Efficient Caching**: Different cache keys are used for responses with and without README HTML content
- **Separation of Concerns**: The MarkdownService handles the conversion, keeping the code modular

## Usage Example

```php
// Get package data with README HTML content
$composerPackagesService = new ComposerPackagesService($repository, $markdownService);
$packages = $composerPackagesService->analyze(includeReadmeHtml: true);

// Access README HTML content
foreach ($packages as $package) {
    $name = $package['name'];
    $readmeHtml = $package['readme_html'];
    
    if ($readmeHtml) {
        echo "Package: $name";
        echo "README HTML: $readmeHtml";
    }
}

// Get cached package data with README HTML content
$cachedPackages = $composerPackagesService->getCached(includeReadmeHtml: true);
```

## Related Files

- `app/DataTransferObjects/ComposerPackageData.php`
- `app/Services/ComposerPackagesService.php`
- `app/Services/MarkdownService.php`
- `tests/Feature/Services/ComposerPackagesServiceTest.php`
