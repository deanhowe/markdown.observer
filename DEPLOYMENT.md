# Markdown Observer - Laravel Cloud Deployment

## Prerequisites
- Laravel Cloud account
- Domain: markdown.observer (already registered)
- GitHub repo connected

## Build Commands
```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Deploy Commands
```bash
php artisan migrate --force
```

## Environment Variables (Laravel Cloud Dashboard)
```
APP_NAME="Markdown Observer"
APP_ENV=production
APP_KEY=[generate via: php artisan key:generate --show]
APP_DEBUG=false
APP_URL=https://markdown.observer

DB_CONNECTION=pgsql
[DB credentials auto-injected by Laravel Cloud]

SESSION_DRIVER=database
SESSION_DOMAIN=.markdown.observer
SESSION_SECURE_COOKIE=true

CACHE_STORE=database
QUEUE_CONNECTION=database

FILESYSTEM_DISK=s3
[S3 credentials auto-injected if Object Storage attached]

MAIL_MAILER=smtp
[Configure mail service]
```

## Resources Needed
1. **Database**: PostgreSQL (auto-provisioned)
2. **Cache**: KV Store (optional, using database for now)
3. **Object Storage**: S3 bucket (optional, not storing files yet)

## Deployment Steps

### 1. Create Application
- Go to Laravel Cloud dashboard
- Create new application: "Markdown Observer"
- Connect GitHub repo
- Select branch: `main`

### 2. Configure Environment
- PHP Version: 8.4
- Node Version: 24
- Add environment variables above
- Set build commands
- Set deploy commands

### 3. Attach Database
- Provision PostgreSQL database
- Laravel Cloud auto-injects credentials

### 4. Deploy
- Push to `main` branch (auto-deploy)
- Or click "Deploy" in dashboard

### 5. Configure Domain
- Add custom domain: markdown.observer
- Follow DNS verification steps
- SSL certificate auto-issued

## Post-Deployment

### Create First User
```bash
# Via Laravel Cloud Commands tab
php artisan tinker
User::create([
    'name' => 'Admin',
    'email' => 'admin@markdown.observer',
    'password' => bcrypt('secure-password'),
    'email_verified_at' => now(),
]);
```

### Test
- Visit https://markdown.observer
- Register/login
- Upload composer.json
- Verify package selection works

## Monitoring
- Enable Nightwatch (optional)
- Check logs in Laravel Cloud dashboard
- Monitor usage/costs

## Scaling
- Start with: 1 instance, 512MB RAM
- Scale up based on usage
- Enable auto-scaling if needed

## Costs (Estimated)
- Compute: ~$10-20/month (small instance)
- Database: ~$10/month (small PostgreSQL)
- Total: ~$20-30/month to start

## Notes
- Ephemeral filesystem (no file storage needed)
- Database-backed sessions/cache
- All docs stored in PostgreSQL
- No git repos needed (HTTP fetch only)
- Ready for multi-tenant scale
