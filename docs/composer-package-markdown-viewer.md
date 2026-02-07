# Package Markdown Viewer

This document describes the Package Markdown Viewer feature, which allows you to view Markdown files associated with Composer packages in your application.

## Overview

The Package Markdown Viewer provides a way to:

1. Analyze Composer packages used in your application
2. Rank packages by their usage in the codebase
3. View Markdown files associated with each package
4. Convert Markdown files to HTML for viewing in the browser
5. Open files directly in PHPStorm from the browser using URL schemes

This feature is useful for:

- Exploring documentation of packages used in your application
- Understanding how packages are used in your codebase
- Finding examples and usage instructions for packages

## How It Works

The Package Markdown Viewer consists of several components:

1. **AnalyzeComposerPackages Command**: A console command that analyzes Composer packages and their usage in the application, and generates a JSON file with package details.
2. **PackageMarkdownService**: A service that provides methods to access package data and Markdown files.
3. **PackageMarkdownController**: A controller that serves package data and Markdown files to the frontend.
4. **Filesystem Configuration**: A filesystem disk configuration that allows accessing Markdown files in the vendor directory.

### Package Analysis

The package analysis process:

1. Reads the composer.json file to get all packages (both prod and dev)
2. Analyzes the codebase to determine how frequently each package is used
3. Finds Markdown files associated with each package in the vendor directory
4. Converts full file paths to relative paths for better portability
5. Generates PHPStorm URL schemes for each file to enable direct opening in the IDE
6. Ranks packages by usage count
7. Saves the data to database/data/composer-details.json

### Viewing Markdown Files

The Package Markdown Viewer provides several ways to view Markdown files:

1. **Package List**: View a list of all packages, sorted by usage rank
2. **Package Details**: View details of a specific package, including its Markdown files
3. **Markdown Viewer**: View a specific Markdown file, converted to HTML
4. **Raw Markdown**: View the raw Markdown content
5. **HTML**: View the HTML generated from the Markdown
6. **PHPStorm Integration**: Open files directly in PHPStorm from the browser using URL schemes

## Usage

### Analyzing Packages

To analyze packages and generate the composer-details.json file, run:

```bash
php artisan app:analyze-composer-packages
```

This command will:
1. Analyze all Composer packages in your application
2. Determine how frequently each package is used
3. Find Markdown files associated with each package
4. Generate a JSON file with package details

### Viewing Packages

To view packages and their Markdown files, visit the following routes:

- `/packages`: View a list of all packages
- `/packages/{package}`: View details of a specific package
- `/packages/{package}/markdown/{filePath}`: View a specific Markdown file
- `/packages/{package}/raw/{filePath}`: View the raw Markdown content
- `/packages/{package}/html/{filePath}`: View the HTML generated from the Markdown

### Refreshing Package Data

To refresh the package data, visit:

- `/packages/refresh`: Refresh the package data by running the analysis command

## Implementation Details

### AnalyzeComposerPackages Command

The `AnalyzeComposerPackages` command is responsible for analyzing Composer packages and their usage in the application. It:

1. Reads the composer.json file to get all packages
2. Analyzes the codebase to determine how frequently each package is used
3. Finds Markdown files associated with each package
4. Converts full file paths to relative paths using the `getRelativePath` method
5. Generates PHPStorm URL schemes for each file using the `getPhpStormUrl` method
6. Generates a JSON file with package details

### PackageMarkdownService

The `PackageMarkdownService` provides methods to access package data and Markdown files:

- `getAllPackages()`: Get all packages with their details
- `getPackagesByType(string $type)`: Get packages of a specific type (prod or dev)
- `getPackage(string $name)`: Get a specific package by name
- `getPackageMarkdownFiles(string $packageName)`: Get all Markdown files for a specific package
- `getPackageMarkdownFile(string $packageName, string $filePath)`: Get a specific Markdown file for a package
- `getPackageMarkdownContent(string $packageName, string $filePath)`: Get the content of a specific Markdown file
- `getPackagesByRank()`: Get packages sorted by rank
- `getTopPackages(int $limit)`: Get the top N most used packages
- `refreshPackageData()`: Refresh the package data by running the analysis command
- `getPhpStormUrl(string $packageName, string $filePath)`: Get the PHPStorm URL for a file
- `getRelativePath(string $packageName, string $filePath)`: Get the relative path for a file

### PackageMarkdownController

The `PackageMarkdownController` serves package data and Markdown files to the frontend:

- `index()`: Display a listing of all packages
- `show(string $packageName)`: Display details of a specific package
- `showMarkdownFile(string $packageName, string $filePath)`: Display a specific Markdown file, including PHPStorm URL for direct opening
- `refresh()`: Refresh the package data
- `getRawMarkdown(string $packageName, string $filePath)`: Get the raw Markdown content
- `getHtml(string $packageName, string $filePath)`: Get the HTML content

### Filesystem Configuration

The Package Markdown Viewer uses a custom filesystem disk configuration to access Markdown files in the vendor directory:

```php
'package-markdown' => [
    'driver' => 'local',
    'root' => base_path('vendor'),
    'throw' => false,
    'report' => false,
    'visibility' => 'public',
],
```

## Conclusion

The Package Markdown Viewer provides a convenient way to explore Markdown files associated with Composer packages in your application. It helps you understand how packages are used in your codebase and provides easy access to package documentation.

With the addition of relative paths and PHPStorm URL schemes, the Package Markdown Viewer now offers enhanced developer experience by:

1. Using relative paths instead of full paths for better portability across different environments
2. Providing direct links to open files in PHPStorm from the browser, streamlining the workflow between documentation and code
3. Making it easier to navigate between package documentation and the actual implementation
