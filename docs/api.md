# API Documentation

This document provides comprehensive documentation for the API endpoints available in this application.

## API Versioning (we have it, but it *should* never break)

All API endpoints are versioned to ensure backward compatibility. The current version is `v0.0`.

API requests should be prefixed with `/api/<endpoint>`.

We promise to not break the API once it is released.

## Authentication

All API endpoints require authentication using Laravel Sanctum. You need to include a valid authentication token in the request headers.

```
Authorization: Bearer <your-token>
```

## Response Format

All API responses follow a consistent format:

```json
{
    "data": {
        "id": 1,
        "name": "Example Resource",
        "description": "This is an example resource"
    },
    "links": {
        "self": "https://example.com/api/resource/1"
    },
    "meta": {
        "api_version": "v0.0"
    },
    "message": "Operation successful"
}
```

For collections, the format is:

```json
{
    "data": [
        {
            "id": 1,
            "name": "Example Resource 1",
            "description": "This is the first example resource"
        },
        {
            "id": 2,
            "name": "Example Resource 2",
            "description": "This is the second example resource"
        }
    ],
    "links": {
        "self": "https://example.com/api/resources"
    },
    "meta": {
        "api_version": "v0.0",
        "total_count": 2
    }
}
```

## Error Handling

Errors are returned with appropriate HTTP status codes and a consistent format:

```json
{
    "message": "Error message",
    "meta": {
        "api_version": "v0.0"
    }
}
```

Common HTTP status codes:
- 200: OK
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Unprocessable Entity
- 500: Internal Server Error

## Endpoints

### Pages

#### List All Pages

```
GET /api/pages
```

**Response:**

```json
{
    "data": [
        {
            "filename": "welcome.md",
            "markdown_content": "# Welcome\n\nThis is a welcome page.",
            "html_content": "<h1>Welcome</h1><p>This is a welcome page.</p>",
            "links": {
                "self": "https://example.com/api/pages/welcome.md"
            }
        },
        {
            "filename": "about.md",
            "markdown_content": "# About\n\nThis is an about page.",
            "html_content": "<h1>About</h1><p>This is an about page.</p>",
            "links": {
                "self": "https://example.com/api/pages/about.md"
            }
        }
    ],
    "links": {
        "self": "https://example.com/api/pages"
    },
    "meta": {
        "api_version": "v0.0",
        "total_count": 2
    }
}
```

#### Get a Specific Page

```
GET /api/pages/{filename}
```

**Parameters:**
- `filename`: The filename of the page to retrieve

**Response:**

```json
{
    "data": {
        "filename": "welcome.md",
        "markdown_content": "# Welcome\n\nThis is a welcome page.",
        "html_content": "<h1>Welcome</h1><p>This is a welcome page.</p>",
        "tiptap_json": {
            "type": "doc",
            "content": [
                {
                    "type": "heading",
                    "attrs": {
                        "level": 1
                    },
                    "content": [
                        {
                            "type": "text",
                            "text": "Welcome"
                        }
                    ]
                },
                {
                    "type": "paragraph",
                    "content": [
                        {
                            "type": "text",
                            "text": "This is a welcome page."
                        }
                    ]
                }
            ]
        },
        "last_modified": "2023-06-15T10:30:00.000000Z",
        "links": {
            "self": "https://example.com/api/pages/welcome.md"
        }
    },
    "meta": {
        "api_version": "v0.0"
    }
}
```

#### Create a New Page

```
POST /api/pages
```

**Request Body:**

```json
{
    "filename": "new-page.md",
    "content": "# New Page\n\nThis is a new page.",
    "tiptap_json": {
        "type": "doc",
        "content": [
            {
                "type": "heading",
                "attrs": {
                    "level": 1
                },
                "content": [
                    {
                        "type": "text",
                        "text": "New Page"
                    }
                ]
            },
            {
                "type": "paragraph",
                "content": [
                    {
                        "type": "text",
                        "text": "This is a new page."
                    }
                ]
            }
        ]
    }
}
```

**Response:**

```json
{
    "data": {
        "filename": "new-page.md",
        "markdown_content": "# New Page\n\nThis is a new page.",
        "html_content": "<h1>New Page</h1><p>This is a new page.</p>",
        "tiptap_json": {
            "type": "doc",
            "content": [
                {
                    "type": "heading",
                    "attrs": {
                        "level": 1
                    },
                    "content": [
                        {
                            "type": "text",
                            "text": "New Page"
                        }
                    ]
                },
                {
                    "type": "paragraph",
                    "content": [
                        {
                            "type": "text",
                            "text": "This is a new page."
                        }
                    ]
                }
            ]
        },
        "links": {
            "self": "https://example.com/api/pages/new-page.md"
        }
    },
    "message": "Page created successfully",
    "meta": {
        "api_version": "v0.0"
    }
}
```

#### Update a Page

```
PUT /api/pages/{filename}
```

**Parameters:**
- `filename`: The filename of the page to update

**Request Body:**

```json
{
    "content": "# Updated Page\n\nThis page has been updated.",
    "tiptap_json": {
        "type": "doc",
        "content": [
            {
                "type": "heading",
                "attrs": {
                    "level": 1
                },
                "content": [
                    {
                        "type": "text",
                        "text": "Updated Page"
                    }
                ]
            },
            {
                "type": "paragraph",
                "content": [
                    {
                        "type": "text",
                        "text": "This page has been updated."
                    }
                ]
            }
        ]
    }
}
```

**Response:**

```json
{
    "data": {
        "filename": "welcome.md",
        "markdown_content": "# Updated Page\n\nThis page has been updated.",
        "html_content": "<h1>Updated Page</h1><p>This page has been updated.</p>",
        "tiptap_json": {
            "type": "doc",
            "content": [
                {
                    "type": "heading",
                    "attrs": {
                        "level": 1
                    },
                    "content": [
                        {
                            "type": "text",
                            "text": "Updated Page"
                        }
                    ]
                },
                {
                    "type": "paragraph",
                    "content": [
                        {
                            "type": "text",
                            "text": "This page has been updated."
                        }
                    ]
                }
            ]
        },
        "links": {
            "self": "https://example.com/api/pages/welcome.md"
        }
    },
    "message": "Page updated successfully",
    "meta": {
        "api_version": "v0.0"
    }
}
```

#### Delete a Page

```
DELETE /api/pages/{filename}
```

**Parameters:**
- `filename`: The filename of the page to delete

**Response:**

```json
{
    "message": "Page deleted successfully",
    "meta": {
        "api_version": "v0.0"
    }
}
```

### Markdown Conversion

#### Convert Markdown to HTML

```
POST /api/markdown/to-html
```

**Request Body:**

```json
{
    "markdown": "# Heading\n\nThis is a paragraph."
}
```

**Response:**

```json
{
    "html": "<h1>Heading</h1><p>This is a paragraph.</p>",
    "meta": {
        "api_version": "v0.0"
    }
}
```

#### Convert HTML to Markdown

```
POST /api/markdown/to-markdown
```

**Request Body:**

```json
{
    "html": "<h1>Heading</h1><p>This is a paragraph.</p>"
}
```

**Response:**

```json
{
    "markdown": "# Heading\n\nThis is a paragraph.",
    "meta": {
        "api_version": "v0.0"
    }
}
```

### Composer Packages

#### Get Packages for Carousel

```
GET /api/packages/carousel
```

**Description**:
Returns a list of composer packages with their logos and README HTML content, optimized for display in a carousel/slideshow. The endpoint returns up to 10 packages that have logos.

**Response:**

```json
{
    "packages": [
        {
            "name": "spatie/laravel-markdown",
            "logo": {
                "path": "vendor/spatie/laravel-markdown/art/logo.png",
                "url": "data:image/png;base64,..."
            },
            "readme_html": "<h1>Laravel Markdown</h1><p>A highly configurable markdown renderer and Blade component for Laravel.</p>...",
            "rank": 1,
            "type": "prod",
            "version": "1.3.0",
            "description": "A highly configurable markdown renderer and Blade component for Laravel"
        },
        {
            "name": "inertiajs/inertia-laravel",
            "logo": {
                "path": "vendor/inertiajs/inertia-laravel/logo.svg",
                "url": "data:image/svg+xml;base64,..."
            },
            "readme_html": "<h1>Inertia.js Laravel Adapter</h1><p>Visit <a href=\"https://inertiajs.com/\">inertiajs.com</a> to learn more.</p>...",
            "rank": 2,
            "type": "prod",
            "version": "0.6.9",
            "description": "The Laravel adapter for Inertia.js"
        }
    ]
}
```

**Notes**:
- The `logo.url` field contains a data URI representation of the package logo, which can be used directly in an `<img>` tag's `src` attribute.
- The `readme_html` field contains the HTML-rendered content of the package's README.md file.
- Packages are returned in order of their `rank` value (lower rank = higher priority).
- No pagination is provided as the endpoint is limited to returning 10 packages.

## Backward Compatibility

To ensure backward compatibility, the following principles are followed:

1. **Never remove fields**: Once a field is included in a response, it will always be included in future versions.
2. **Optional fields for new requirements**: New fields are added as optional to avoid breaking existing clients.
3. **Versioning for breaking changes**: If a breaking change is necessary, a new API version will be created.
4. **Deprecation notices**: Fields or endpoints that will be deprecated in future versions will be marked with deprecation notices.
