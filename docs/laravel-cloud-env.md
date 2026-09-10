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

⚠️ Variable names below match `config/services.php` — earlier versions of this
doc used `STRIPE_PRO_PRICE_ID`/`STRIPE_LIFETIME_PRICE_ID`, which the app never
reads. If production still has those names, the price lookups return null.

```env
STRIPE_KEY=pk_live_YOUR_KEY
STRIPE_SECRET=sk_live_YOUR_SECRET
STRIPE_WEBHOOK_SECRET=whsec_YOUR_SECRET
STRIPE_PRICE_PRO_MONTHLY=price_YOUR_MONTHLY_ID
STRIPE_PRICE_PRO_YEARLY=price_YOUR_YEARLY_ID
STRIPE_PRICE_LIFETIME=price_YOUR_LIFETIME_ID
```

**STRIPE_WEBHOOK_SECRET is not optional.** Cashier only verifies webhook
signatures when it is set — without it, `POST /stripe/webhook` accepts
unsigned events and a spoofed `customer.subscription.*` event grants free Pro.
Create the endpoint at Stripe dashboard → Developers → Webhooks →
`https://markdown.observer/stripe/webhook`, subscribe to the events listed in
the [Cashier docs](https://laravel.com/docs/12.x/billing#handling-stripe-webhooks),
and copy the signing secret here.

## AI Steering Docs Crawler (ai.markdown.observer)

```env
GITHUB_TOKEN=ghp_YOUR_TOKEN
```

**Not optional if the crawler is meant to make real progress.** Without this,
`App\Jobs\CrawlRepoSteeringDocs` calls the GitHub API unauthenticated - a hard
60 requests/hour cap - against ~500 target repos x up to 6 folders each to
check. That's the most likely reason `ai.markdown.observer` was still showing
0 repos crawled as of 2026-09-10: the job silently rate-limits itself into
invisible progress rather than failing loudly. A classic PAT with no special
scopes is enough (it only reads public repo contents via unauthenticated-eligible
endpoints, just needs the higher authenticated rate limit).

Also verify the "Queue workers enabled" checklist item below was actually
ticked for this environment - the crawl command only *queues* jobs
(`crawl:steering-docs` dispatches, it doesn't process), so nothing runs at
all without a live queue worker regardless of this token.

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
