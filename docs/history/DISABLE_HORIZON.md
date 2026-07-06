# Quick Fix: Disable Horizon

Horizon requires Redis which isn't configured yet.

## Temporary Fix

Comment out Horizon service provider in `config/app.php`:

```php
// App\Providers\HorizonServiceProvider::class,
```

Or add to `.env`:
```
HORIZON_ENABLED=false
```

Then in `app/Providers/HorizonServiceProvider.php`:
```php
public function register()
{
    if (!config('horizon.enabled', true)) {
        return;
    }
    // ... rest of code
}
```

## Better Fix: Add Redis

In Laravel Cloud dashboard:
1. Add KV Store (Redis)
2. Credentials auto-injected
3. Horizon works automatically

## For Now

Queue still works with database driver. Just can't monitor in Horizon.

Check jobs manually:
```bash
php artisan tinker --execute="echo 'Pending: ' . DB::table('jobs')->count() . ', Failed: ' . DB::table('failed_jobs')->count();"
```
