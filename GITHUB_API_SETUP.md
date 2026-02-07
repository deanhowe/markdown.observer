# GitHub API Setup for Steering Docs Crawler

## Rate Limits (Be Nice!)

### Without Token
- **60 requests/hour** - Too low for crawling

### With Personal Access Token
- **5,000 requests/hour** - Perfect for crawling
- Resets every hour
- Check remaining: `curl -H "Authorization: token YOUR_TOKEN" https://api.github.com/rate_limit`

### Best Practices
1. **Sleep between requests** - Already doing 1 second (built into job)
2. **Check rate limit** - Stop if getting close to limit
3. **Respect 403s** - Some repos may block API access
4. **Cache results** - Don't re-crawl same repo/folder

## Creating GitHub Token

### Step 1: Go to GitHub Settings
https://github.com/settings/tokens

### Step 2: Generate New Token (Classic)
Click "Generate new token (classic)"

### Step 3: Set Permissions
**Only need PUBLIC repo access:**

✅ **public_repo** - Access public repositories

That's it! No other permissions needed.

### Step 4: Copy Token
Copy the token (starts with `ghp_`)

### Step 5: Add to .env
```bash
GITHUB_TOKEN=ghp_your_token_here
```

### Step 6: Add to config/services.php
```php
'github' => [
    'token' => env('GITHUB_TOKEN'),
],
```

## Scaling Strategy

### Phase 1: Top 100 Repos (Current)
- 18 repos × 6 folders = 108 API calls
- ~2 minutes with queue
- Well under rate limit

### Phase 2: Top 1,000 Repos
- 1,000 repos × 6 folders = 6,000 API calls
- Would hit rate limit in 1 hour
- **Solution:** Spread over 2 hours with throttling

### Phase 3: All Public Repos with Stars > 100
- Millions of repos
- **Solution:** 
  - Run continuously in background
  - Throttle to 4,000 requests/hour (buffer)
  - Takes days/weeks, that's fine!
  - Re-crawl monthly for updates

## Throttling Implementation

Add to job:

```php
public $tries = 3;
public $backoff = 60; // Retry after 60 seconds if rate limited

public function middleware()
{
    return [new RateLimited('github-api')];
}
```

Add to `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('github-api', function () {
    return Limit::perHour(4000); // Stay under 5k limit
});
```

## Monitoring

### Check Rate Limit Status
```bash
php artisan tinker
>>> Http::withToken(config('services.github.token'))->get('https://api.github.com/rate_limit')->json()
```

### Check Crawler Progress
```bash
# How many collections found?
php artisan tinker
>>> App\Models\SteeringCollection::count()

# How many docs downloaded?
>>> App\Models\SteeringDoc::count()

# Which repos have steering docs?
>>> App\Models\SteeringCollection::pluck('name')
```

## Respectful Crawling

### DO:
✅ Use token (5k/hour limit)
✅ Sleep between requests (1 second)
✅ Handle 403/404 gracefully
✅ Cache results (don't re-crawl)
✅ Throttle to stay under limit
✅ Only access public repos

### DON'T:
❌ Hammer API without token
❌ Ignore rate limit headers
❌ Re-crawl same repo repeatedly
❌ Request private repos
❌ Bypass rate limits with multiple tokens

## GitHub's Perspective

**They WANT you to do this:**
- Public repos are meant to be accessed
- API is designed for this use case
- Rate limits are generous (5k/hour)
- You're building something useful

**Just be respectful:**
- Stay under rate limits
- Don't abuse the API
- Cache results
- Spread load over time

## Production Deployment

### Laravel Cloud Setup
1. Add `GITHUB_TOKEN` to environment variables
2. Ensure queue workers are running
3. Run crawler: `php artisan crawl:steering-docs`
4. Monitor in Horizon

### Continuous Crawling
Add to scheduler (`app/Console/Kernel.php`):

```php
protected function schedule(Schedule $schedule)
{
    // Crawl new repos daily
    $schedule->command('crawl:steering-docs')->daily();
    
    // Re-crawl existing repos weekly (check for updates)
    $schedule->command('crawl:steering-docs --refresh')->weekly();
}
```

## Expected Results

### After 1 Hour
- ~100 repos crawled
- ~10-20 steering doc collections found
- ~50-200 individual docs

### After 1 Day
- ~2,400 repos crawled
- ~100-300 steering doc collections
- ~500-2,000 individual docs

### After 1 Week
- ~16,800 repos crawled
- ~500-1,000 steering doc collections
- ~2,000-10,000 individual docs

**This is HUGE.** No one else has this data.

## Legal/Ethical

✅ **Legal:** Public repos, public API, within rate limits
✅ **Ethical:** Building useful tool, respecting limits
✅ **Attribution:** Store repo name, link back to source
✅ **License:** Respect repo licenses (most are MIT/Apache)

## Summary

**Token Permissions:** `public_repo` only
**Rate Limit:** 5,000/hour
**Strategy:** Throttle to 4,000/hour for safety
**Timeline:** Days/weeks to crawl everything
**Result:** World's first steering docs search engine

---

*Be nice. Stay under limits. Build something amazing.* 🚀
