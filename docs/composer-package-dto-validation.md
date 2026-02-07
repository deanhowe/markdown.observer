# ComposerPackageData DTO Validation

## Overview

This document describes the validation features added to the `ComposerPackageData` DTO to ensure data integrity and graceful failure handling.

## Changes Made

1. Added validation attributes to the `ComposerPackageData` DTO properties:
   - `name`: Required, must be a string
   - `version`: Required, must be a string
   - `description`: Must be a string, defaults to empty string
   - `homepage`: Nullable, must be a string when provided
   - `directDependency`: Nullable, converted to boolean
   - `source`: Nullable, must be a string when provided
   - `abandoned`: Nullable, converted to boolean
   - `dependencies`: Must be an array
   - `isDev`: Must be a boolean

2. Changed the type of `directDependency` and `abandoned` from `?string` to `?bool` to better match their actual data types.

3. Added a new static method `fromArrayWithValidation` that:
   - Converts string boolean values to actual booleans
   - Ensures required fields have default values if missing
   - Catches validation exceptions and creates a minimal valid object with default values
   - Logs validation errors for debugging purposes

4. Updated the `ComposerPackagesRepository` to use the new `fromArrayWithValidation` method and handle any exceptions that might occur during the creation of the `ComposerPackageData` object.

5. Maintained the custom `toArray()` method to preserve the field mappings (like 'direct-dependency' instead of 'directDependency') for backward compatibility.

## Benefits

- **Data Integrity**: Validation ensures that the data conforms to the expected types and formats
- **Graceful Failure**: The DTO now fails gracefully by providing default values when validation fails
- **Better Type Safety**: Using proper boolean types for boolean values improves type safety
- **Debugging**: Validation errors are logged for easier debugging
- **Backward Compatibility**: The custom `toArray()` method ensures that the DTO continues to work with existing code

## Usage Example

```php
// Creating a DTO with validation
try {
    $packageData = ComposerPackageData::fromArrayWithValidation([
        'name' => 'spatie/laravel-data',
        'version' => '^4.15',
        'description' => 'A package to handle data objects in Laravel',
        'homepage' => 'https://github.com/spatie/laravel-data',
        'direct-dependency' => 'true', // Will be converted to boolean
        'abandoned' => false,
    ]);
} catch (\Exception $e) {
    // Handle the exception
    logger()->error('Failed to create ComposerPackageData', [
        'error' => $e->getMessage(),
    ]);
    
    // Create a minimal valid object
    $packageData = new ComposerPackageData(
        name: 'unknown',
        version: 'unknown'
    );
}
```

## Spatie Laravel Data Documentation

For more information on validation with Spatie's Laravel Data package, refer to the [official documentation](https://spatie.be/docs/laravel-data).
