# Markdown Editor Documentation

## Overview

The Markdown Editor is a feature-rich editor for creating and editing Markdown files. It provides a user-friendly interface for editing Markdown content, with server-side conversion between Markdown and HTML.

## Features

- **Server-side Markdown Processing**: Uses PHP libraries for converting between Markdown and HTML
- **Revision History**: Tracks changes to Markdown files in a database
- **Mobile-friendly Interface**: Uses a slide-out sheet for file selection
- **Preview Mode**: Instantly preview Markdown as HTML
- **Markdown Help**: Built-in guide for Markdown syntax

## Components

### Backend

#### MarkdownService

The `MarkdownService` handles conversion between Markdown and HTML using the following libraries:
- `spatie/laravel-markdown`: Converts Markdown to HTML
- `league/html-to-markdown`: Converts HTML to Markdown
- `stevebauman/purify`: Sanitizes HTML to prevent XSS attacks

```php
// Example usage
$markdownService = app(App\Services\MarkdownService::class);
$html = $markdownService->toHtml('# Heading');
$markdown = $markdownService->toMarkdown('<h1>Heading</h1>');
```

#### PageRevision Model

The `PageRevision` model tracks changes to Markdown files, storing:
- Filename
- Markdown content
- HTML content
- Tiptap JSON (optional)
- Revision type (create, update, delete)

```php
// Example usage
$revision = PageRevision::createRevision(
    'example',
    '# Example',
    '<h1>Example</h1>',
    null,
    'create'
);
```

#### PageController

The `PageController` provides API endpoints for:
- Listing pages
- Creating pages
- Updating pages
- Deleting pages
- Converting Markdown to HTML
- Converting HTML to Markdown

### Frontend

#### MarkdownEditor

The `MarkdownEditor` component provides a textarea for editing Markdown with:
- Preview tab that shows rendered HTML
- Markdown help sheet with syntax examples

```jsx
// Example usage
<MarkdownEditor
    content={markdownContent}
    onChange={handleContentChange}
    placeholder="Write your markdown here..."
/>
```

#### PageManagerSheet

The `PageManagerSheet` component provides a slide-out sheet for managing pages with:
- List of pages
- Create, save, and delete buttons
- Mobile-friendly interface

```jsx
// Example usage
<PageManagerSheet
    onPageSelect={handlePageSelect}
    currentContent={markdownContent}
    currentFilename={currentFilename}
/>
```

## API Endpoints

### Page Management

- `GET /api/pages`: List all pages
- `POST /api/pages`: Create a new page
- `GET /api/pages/{filename}`: Get a specific page
- `PUT /api/pages/{filename}`: Update a specific page
- `DELETE /api/pages/{filename}`: Delete a specific page

### Markdown Conversion

- `POST /api/markdown/to-html`: Convert Markdown to HTML
- `POST /api/markdown/to-markdown`: Convert HTML to Markdown

## Database Schema

The `page_revisions` table has the following schema:

| Column | Type | Description |
|--------|------|-------------|
| id | integer | Primary key |
| filename | string | Name of the Markdown file |
| markdown_content | text | Raw Markdown content |
| html_content | text | Rendered HTML content |
| tiptap_json | json | Tiptap editor state (optional) |
| revision_type | string | Type of revision (create, update, delete) |
| created_at | datetime | Creation timestamp |
| updated_at | datetime | Update timestamp |
