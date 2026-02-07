# Laravel Cloud Queue Setup

## Current Status
✅ Queue jobs created (CrawlRepoSteeringDocs, FetchPackageDocsJob)
✅ Database queue driver configured
✅ Horizon installed and configured
✅ Code deployed to production

## Enable Queue Workers in Laravel Cloud

### Step 1: Go to Laravel Cloud Dashboard
https://cloud.laravel.com

### Step 2: Select Your Project
Click on "markdown.observer"

### Step 3: Go to "Workers" Tab
Should see queue worker configuration

### Step 4: Enable Queue Worker
- **Connection:** database (or redis if KV Store attached)
- **Queue:** default
- **Processes:** 1 (start small, scale up later)
- **Timeout:** 60 seconds
- **Memory:** 128MB

### Step 5: Save and Deploy
Workers will start automatically

## Verify It's Working

### Check Horizon
Visit: https://markdown.observer/horizon

Should see:
- Active workers
- Pending jobs
- Completed jobs
- Failed jobs

### Run Crawler
```bash
# SSH into Laravel Cloud or run via Tinker
php artisan crawl:steering-docs
```

### Watch Jobs Process
In Horizon, you'll see:
- 18 jobs queued (one per repo)
- Jobs processing
- Completed/failed counts

## If Workers Not Available

### Alternative: Run Manually
```bash
# SSH into production
php artisan queue:work --tries=2 --timeout=60
```

### Or: Use Scheduled Task
Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Process queue every minute
    $schedule->command('queue:work --stop-when-empty')->everyMinute();
}
```

## Environment Variables Needed

Already set:
- ✅ `QUEUE_CONNECTION=database`
- ✅ `HORIZON_EMAILS=deanhowe@gmail.com`

Need to add:
- `GITHUB_TOKEN=ghp_your_token_here`

## Testing Locally

```bash
# Start queue worker
php artisan queue:work

# In another terminal, run crawler
php artisan crawl:steering-docs

# Watch Horizon
open http://markdown.observer.test/horizon
```

## Troubleshooting

### Jobs Not Processing
1. Check workers are enabled in Laravel Cloud
2. Check `jobs` table has pending jobs: `SELECT * FROM jobs;`
3. Check `failed_jobs` table: `SELECT * FROM failed_jobs;`
4. Check Horizon is accessible: `/horizon`

### Rate Limit Errors
- Add `GITHUB_TOKEN` to environment variables
- Check token has `public_repo` permission
- Verify token hasn't expired

### Memory Issues
- Increase worker memory in Laravel Cloud
- Or reduce batch size in crawler

## Expected Timeline

With 1 worker:
- 18 repos × ~10 seconds each = ~3 minutes
- Some repos have no steering docs (fast)
- Some have multiple folders (slower)

With 3 workers:
- ~1 minute for all 18 repos

## Next Steps

1. ✅ Code deployed
2. ⏳ Enable queue workers in Laravel Cloud dashboard
3. ⏳ Add `GITHUB_TOKEN` to environment variables
4. ⏳ Run `php artisan crawl:steering-docs`
5. ⏳ Watch Horizon for results
6. ⏳ Check database: `SELECT COUNT(*) FROM steering_collections;`

---

**Once workers are enabled, the crawler will run automatically!** 🚀
