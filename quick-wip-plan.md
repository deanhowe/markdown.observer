# Quick WIP Plan: Composer Package Analysis Refactoring

## Overview

This document outlines the changes made to refactor the composer package analysis code and future improvements that could be made. The main goal was to ensure that we never use `composer.json` directly to determine package information, but instead use the `ComposerPackagesRepository` which runs `composer` via a process to get more detailed information.

## Changes Made

### 1. ~~Refactored `AnalyzeComposerPackages` Command~~

- ~~Modified the command to use `ComposerPackagesRepository` instead of reading directly from `composer.json`~~
- ~~Injected the repository and a new analysis service into the command~~
- ~~Updated the command to save the composer.json checksum for quick reference~~

### 2. ~~Created `ComposerPackageAnalysisService`~~

- ~~Moved all private functions from `AnalyzeComposerPackages` to this service~~
- ~~Made the functions public or private as appropriate~~
- ~~Improved code organization and separation of concerns~~
- ~~Enhanced to store README HTML content directly in the JSON file~~
- ~~Added functionality to build and store a directory tree of markdown files in the package~~

### 3. ~~Created `ComposerPackage` Model with Sushi~~

- ~~Implemented a Sushi model to provide an Eloquent interface to the package data~~
- ~~Added methods to get packages by type, rank, and with logos~~
- ~~Added a method to check if the data needs to be refreshed by comparing checksums~~
- ~~Added a method to refresh the data by running the analysis command~~

### 4. ~~Updated `GetPackagesForCarouselAction`~~

- ~~Modified the action to use the `ComposerPackage` model instead of reading from the JSON file~~
- ~~Added automatic refresh of package data when needed~~
- ~~Improved error handling and data validation~~
- ~~Updated to use README HTML content directly from the model instead of retrieving and converting it from the filesystem~~
- ~~Removed dependency on `PackageMarkdownService` for README content~~
- ~~Added support for including the directory tree of markdown files in the returned data~~

## Future Improvements

### 1. ~~Further Refactoring~~

- ~~Consider moving more functionality from `PackageMarkdownService` to the `ComposerPackage` model~~
- ~~Create a dedicated service for handling package logos and README files~~
- ~~Implement a more robust caching mechanism for package data~~

### 2. ~~Performance Optimizations~~

- ~~Optimize the package analysis process to reduce execution time~~
- ~~Implement background processing for package analysis using Laravel's queue system~~
- ~~Add more granular caching to avoid unnecessary processing~~

### 3. ~~Enhanced Features~~

- ~~Add more detailed package information from Packagist API~~
- ~~Implement version comparison and update notifications~~
- ~~Add support for private repositories and custom package sources~~

### 4. ~~UI Improvements~~

- ~~Enhance the carousel UI to show more package details~~
- ~~Add filtering and sorting options for packages~~
- ~~Implement a dedicated page for each package with detailed information~~

### 5. ~~Testing~~

- ~~Add unit tests for the new classes and methods~~
- ~~Add integration tests for the package analysis process~~
- ~~Add browser tests for the carousel UI~~

## Conclusion

The refactoring has improved the code organization, separation of concerns, and performance of the composer package analysis process. The use of Sushi provides an elegant way to work with the package data using Eloquent, and the automatic refresh mechanism ensures that the data is always up to date.

By storing README HTML content directly in the model's data source, we've eliminated the need for filesystem access and markdown conversion when retrieving carousel data. This improves performance and reduces dependencies between components. All the data needed for the carousel is now available directly from the `ComposerPackage` model, making the code more maintainable and efficient.

The addition of a directory tree structure for markdown files in each package provides a more organized way to navigate the documentation files. This makes it easier for users to find and access the documentation they need, enhancing the overall user experience. The directory tree is built automatically during the package analysis process and is included in the data returned by the carousel action, making it readily available for the frontend to display.
