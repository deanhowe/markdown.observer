# ComposerPackagesService Logo Integration

## Overview

This document describes the changes made to the `ComposerPackagesService` and `ComposerPackageData` DTO to include logo information for composer packages.

## Changes Made

1. Extended the `ComposerPackageData` DTO to include a logo field:
   - Added a nullable array field `logo` to store logo information
   - Updated the `fromArrayWithValidation` method to handle the logo field
   - Updated the `toArray` method to include the logo field in the returned array

2. Enhanced the `ComposerPackagesService` to handle logo URIs:
   - Added a `getPackageLogo` method to get the logo URI for a package
   - Added helper methods to find and process logo files
   - Updated the `analyze` method to include logo information for each package

3. Implemented logo detection and storage logic:
   - Looks for logo files in the art directory or files named 'logo'
   - Falls back to extracting images from README.md files
   - Creates a placeholder SVG if no logo is found
   - Stores logos in the public storage disk for web access

## Benefits

- **Richer Data**: Package data now includes logo information, enhancing the UI experience
- **Consistent Implementation**: Logo handling is now part of the service layer, not just in commands
- **Reusable Logic**: Logo detection and storage logic can be reused across the application
- **Better Separation of Concerns**: The service now handles all aspects of package data, including logos

## Usage Example

```php
// Get package data with logo information
$composerPackagesService = new ComposerPackagesService($repository);
$packages = $composerPackagesService->analyze();

// Access logo information
foreach ($packages as $package) {
    $name = $package['name'];
    $logo = $package['logo'];
    
    if ($logo) {
        $logoUrl = $logo['url'];
        echo "Package: $name, Logo URL: $logoUrl";
    }
}

// Get logo for a specific package
$logoData = $composerPackagesService->getPackageLogo('spatie/laravel-data');
if ($logoData) {
    $logoUrl = $logoData['url'];
    echo "Logo URL for spatie/laravel-data: $logoUrl";
}
```

## Related Files

- `app/DataTransferObjects/ComposerPackageData.php`
- `app/Services/ComposerPackagesService.php`
