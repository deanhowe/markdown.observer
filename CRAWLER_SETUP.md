# Steering Docs Crawler - Setup

## Add GitHub Token

1. Add to `.env`:
```bash
GITHUB_TOKEN=your_github_personal_access_token
```

2. Add to `config/services.php`:
```php
'github' => [
    'token' => env('GITHUB_TOKEN'),
],
```

## Run Crawler

```bash
# Crawl top 18 repos
php artisan crawl:steering-docs

# Run in background
nohup php artisan crawl:steering-docs > storage/logs/crawler.log 2>&1 &
```

## What It Does

1. Checks 18 top repos for steering doc folders:
   - `.claude/`
   - `.cursor/`
   - `.ai/`
   - `.kiro/`
   - `.aider/`
   - `.windsurf/`

2. Downloads all files from found folders

3. Stores in database:
   - `steering_collections` - One per repo/folder
   - `steering_docs` - Individual files with content

4. Marks as public for search

## Repos Checked

- facebook/react
- vercel/next.js
- vuejs/core
- angular/angular
- sveltejs/svelte
- laravel/laravel
- symfony/symfony
- livewire/livewire
- filamentphp/filament
- tailwindlabs/tailwindcss
- prettier/prettier
- eslint/eslint
- vitejs/vite
- remix-run/remix
- nuxt/nuxt
- nestjs/nest
- strapi/strapi
- electron/electron

## Next Steps

1. Add more repos to crawl
2. Build search interface
3. Add "What do steering docs for X contain?" search
4. Show patterns, commands, rules
5. Let users fork/clone steering docs

## Database

```sql
-- Check what we found
SELECT name, type, COUNT(*) as doc_count 
FROM steering_collections 
JOIN steering_docs ON steering_collections.id = steering_docs.steering_collection_id 
GROUP BY steering_collections.id;

-- Search steering docs
SELECT * FROM steering_docs WHERE content LIKE '%/verify%';
```
