# ComposerPackageData DTO Update

## Overview

This document describes the changes made to the `ComposerPackageData` DTO and related components to better utilize Spatie's Laravel Data package and include additional fields from the Composer package information.

## Changes Made

1. Updated the `ComposerPackageData` DTO to include additional fields:
   - `directDependency`: Indicates whether the package is a direct dependency
   - `source`: The source URL of the package
   - `abandoned`: Information about package abandonment status

2. Modified the `ComposerPackagesRepository` to use the updated DTO:
   - Changed the `getDependencies` method to return a Collection of `ComposerPackageData` objects instead of arrays
   - Updated the `getRequireString` method to work with the new DTO objects

3. Updated commands that use the repository:
   - Modified `ComposerListDependenciesCommand` to use object property syntax instead of array access
   - Modified `ComposerListDirectDependenciesCommand` to use object property syntax instead of array access

## Benefits

- **Type Safety**: Using a strongly-typed DTO provides better type safety and IDE autocompletion
- **Consistency**: All package data is now consistently represented as objects
- **Maintainability**: Changes to the data structure only need to be made in one place
- **Additional Data**: The DTO now includes all relevant fields from the Composer package information

## Usage Example

```php
// Get all dependencies as ComposerPackageData objects
$dependencies = $composerPackagesRepository->getDependencies();

// Access properties using object syntax
foreach ($dependencies as $package) {
    echo "Name: {$package->name}\n";
    echo "Version: {$package->version}\n";
    echo "Description: {$package->description}\n";
    echo "Homepage: {$package->homepage}\n";
    echo "Direct Dependency: {$package->directDependency}\n";
    echo "Source: {$package->source}\n";
    echo "Abandoned: {$package->abandoned}\n";
}
```

## Related Files

- `app/DataTransferObjects/ComposerPackageData.php`
- `app/Repositories/ComposerPackagesRepository.php`
- `app/Console/Commands/ComposerListDependenciesCommand.php`
- `app/Console/Commands/ComposerListDirectDependenciesCommand.php`
