# Laravel Cloud Environment Variables

Copy these into Laravel Cloud dashboard → Environment → Environment Variables

## Required (Core App)

```env
APP_NAME="Markdown Observer"
APP_ENV=production
APP_KEY=[Click "Generate" button in Laravel Cloud]
APP_DEBUG=false
APP_URL=https://markdown.observer

SESSION_DRIVER=database
SESSION_DOMAIN=.markdown.observer
SESSION_SECURE_COOKIE=true

CACHE_STORE=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_LEVEL=error
```

## Database (Auto-Injected by Laravel Cloud)
**Don't add these - Laravel Cloud adds them automatically when you attach a database**

```env
DB_CONNECTION=pgsql
DB_HOST=[auto]
DB_PORT=[auto]
DB_DATABASE=[auto]
DB_USERNAME=[auto]
DB_PASSWORD=[auto]
```

## Mail (Choose One)

### Option 1: Postmark (Recommended)
```env
MAIL_MAILER=postmark
MAIL_FROM_ADDRESS=hello@markdown.observer
MAIL_FROM_NAME="Markdown Observer"
POSTMARK_TOKEN=your-postmark-token
```

### Option 2: AWS SES
```env
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=hello@markdown.observer
MAIL_FROM_NAME="Markdown Observer"
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
```

### Option 3: Log (Testing Only)
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@markdown.observer
MAIL_FROM_NAME="Markdown Observer"
```

## Stripe (After Setup)

```env
STRIPE_KEY=pk_live_YOUR_KEY
STRIPE_SECRET=sk_live_YOUR_SECRET
STRIPE_WEBHOOK_SECRET=whsec_YOUR_SECRET
STRIPE_PRO_PRICE_ID=price_YOUR_PRO_ID
STRIPE_LIFETIME_PRICE_ID=price_YOUR_LIFETIME_ID
```

## Optional (Add Later)

### Object Storage (If you add S3)
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=[auto if using Laravel Cloud Object Storage]
AWS_SECRET_ACCESS_KEY=[auto]
AWS_DEFAULT_REGION=[auto]
AWS_BUCKET=[auto]
```

### Redis (If you add KV Store)
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=[auto]
REDIS_PASSWORD=[auto]
REDIS_PORT=[auto]
```

### Sentry (Error Tracking)
```env
SENTRY_LARAVEL_DSN=your-sentry-dsn
```

## Checklist

Before deploying:
- [ ] APP_KEY generated
- [ ] APP_URL set to your domain
- [ ] SESSION_DOMAIN set (with leading dot)
- [ ] Mail service configured
- [ ] Stripe keys added (if using payments)

After first deploy:
- [ ] Database attached (credentials auto-added)
- [ ] Queue workers enabled
- [ ] Domain configured
- [ ] SSL certificate issued

## Notes

- **Never commit .env to git**
- Use test keys locally (pk_test_, sk_test_)
- Use live keys in production (pk_live_, sk_live_)
- Laravel Cloud auto-injects database/redis/s3 credentials
- You only need to add your own service keys (Stripe, mail, etc.)
