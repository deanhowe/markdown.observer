# 🚀 SHIP IT - Markdown Observer Deployment

## Pre-Flight Checklist
- [x] Landing page built
- [x] Auth working
- [x] Queue jobs ready
- [x] Horizon installed
- [x] PostgreSQL compatible
- [x] All features complete

## Step 1: Prepare Repository

```bash
cd ~/PLANNR/VALET/VHOSTS/markdown.observer

# Ensure everything is committed
git add .
git commit -m "Production ready - MVP complete"
git push origin main
```

## Step 2: Laravel Cloud Setup

### Create Application
1. Go to https://cloud.laravel.com
2. Click "New Application"
3. Name: "Markdown Observer"
4. Connect GitHub repo
5. Branch: `main`

### Configure Environment

**General Settings:**
- PHP Version: 8.4
- Node Version: 24

**Build Commands:**
```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Deploy Commands:**
```bash
php artisan migrate --force
php artisan horizon:publish
```

**Environment Variables:**
```env
APP_NAME="Markdown Observer"
APP_ENV=production
APP_KEY=[Click "Generate" in Laravel Cloud]
APP_DEBUG=false
APP_URL=https://markdown.observer

SESSION_DRIVER=database
SESSION_DOMAIN=.markdown.observer
SESSION_SECURE_COOKIE=true

CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@markdown.observer
MAIL_FROM_NAME="Markdown Observer"
```

## Step 3: Attach Resources

### Database
1. Click "Attach Database"
2. Select PostgreSQL
3. Size: Small (512MB) - $10/mo
4. Laravel Cloud auto-injects credentials

### Queue Workers (Horizon)
1. Go to "Queues" tab
2. Click "Add Queue"
3. Connection: database
4. Queue: default
5. Workers: 1
6. Enable Horizon dashboard

## Step 4: Deploy

1. Click "Deploy" button
2. Wait for build (~2-3 minutes)
3. Check logs for errors
4. Visit your-app.laravel.cloud domain

## Step 5: Configure Domain

1. Go to "Domains" tab
2. Click "Add Domain"
3. Enter: markdown.observer
4. Follow DNS instructions:
   ```
   A Record: @ → [Laravel Cloud IP]
   CNAME: www → your-app.laravel.cloud
   ```
5. Wait for verification (~5 minutes)
6. SSL certificate auto-issued

## Step 6: Create First User

```bash
# In Laravel Cloud "Commands" tab
php artisan tinker

User::create([
    'name' => 'Admin',
    'email' => 'dean@markdown.observer',
    'password' => bcrypt('secure-password-here'),
    'email_verified_at' => now(),
    'subscription_tier' => 'lifetime',
    'upload_limit' => 999,
    'doc_limit' => 999,
]);
```

## Step 7: Test Production

1. Visit https://markdown.observer
2. Register new account
3. Upload composer.json
4. Select packages
5. Wait for docs to fetch (check /horizon)
6. View docs
7. Edit a doc
8. Sync package

## Step 8: Monitor

- **Horizon:** https://markdown.observer/horizon
- **Logs:** Laravel Cloud dashboard
- **Usage:** Check compute/database metrics

## Costs (Monthly)

- Compute: $10-15 (512MB instance)
- Database: $10 (PostgreSQL small)
- **Total: ~$20-25/month**

## Post-Launch

### Immediate
- [ ] Test full user flow
- [ ] Monitor Horizon for failed jobs
- [ ] Check error logs
- [ ] Verify email works

### Week 1
- [ ] Add Stripe integration
- [ ] Set up email service (Postmark/SES)
- [ ] Add analytics
- [ ] Monitor usage

### Week 2
- [ ] Add scheduled tasks (auto-sync)
- [ ] Upgrade to KV Store (Redis)
- [ ] Add more package sources
- [ ] Marketing push

## Rollback Plan

If something breaks:
```bash
# In Laravel Cloud
1. Go to "Deployments"
2. Find last working deployment
3. Click "Redeploy"
```

## Support

- Laravel Cloud: https://cloud.laravel.com/docs
- Discord: https://discord.gg/laravel
- Email: support@laravel.com

---

## 🎉 YOU'RE LIVE!

Visit: https://markdown.observer

Built in one night. Shipped to production. LFG! 🚀
