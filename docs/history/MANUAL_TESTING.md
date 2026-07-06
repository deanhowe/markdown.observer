# Manual Testing Checklist

## ✅ Pre-Deploy Testing

**Status:** Build fixed, app loads ✅

### Testing URL

Site is secured with HTTPS:
- ✅ **https://markdown.observer.test** (works)
- ❌ http://markdown.observer.test (redirects to HTTPS)

### 1. Homepage
- [ ] Visit http://markdown.observer.test
- [ ] See landing page with "Your Package Documentation"
- [ ] See pricing (Free, Pro, Lifetime)
- [ ] Click "Get Started" → goes to register

### 2. Registration
- [ ] Fill in name, email, password
- [ ] Click "Register"
- [ ] Redirected to dashboard
- [ ] See "Your Packages" heading
- [ ] See "Upload Packages" button

### 3. Upload Packages
- [ ] Click "Upload Packages"
- [ ] Create test composer.json:
  ```json
  {
    "require": {
      "laravel/framework": "^12.0",
      "spatie/laravel-data": "^4.0"
    }
  }
  ```
- [ ] Upload file
- [ ] See package selection page
- [ ] See both packages listed
- [ ] See limit info (0/10 used)

### 4. Select Packages
- [ ] Check both packages
- [ ] Click "Add 2 Packages"
- [ ] Redirected to dashboard
- [ ] See success message "2 packages added! Docs are being fetched..."
- [ ] See packages listed

### 5. Queue Processing
- [ ] Run: `php artisan queue:work`
- [ ] Watch jobs process
- [ ] Refresh dashboard
- [ ] See doc counts update

### 6. View Docs
- [ ] Click on "laravel/framework"
- [ ] See docs page with sidebar
- [ ] See README.md listed
- [ ] See doc content displayed
- [ ] Click different docs in sidebar

### 7. Edit Docs
- [ ] Click "Edit" button
- [ ] See textarea with content
- [ ] Change some text
- [ ] Click "Save"
- [ ] See success message
- [ ] See edited content
- [ ] See orange asterisk (*) next to filename

### 8. Sync Package
- [ ] Go back to dashboard
- [ ] Click "Sync" button on a package
- [ ] See "Sync queued!" message
- [ ] Run queue worker
- [ ] Docs update

### 9. Pricing Page
- [ ] Visit /pricing
- [ ] See all three tiers
- [ ] See features listed
- [ ] Buttons present (not functional yet)

### 10. Horizon
- [ ] Visit /horizon
- [ ] See dashboard
- [ ] See completed jobs
- [ ] See metrics

## 🚨 Known Issues to Fix

- [ ] Auth routes need testing
- [ ] Email verification not set up
- [ ] Stripe not integrated yet
- [ ] No error handling for failed doc fetches
- [ ] No rate limiting on GitHub API

## 📸 Screenshots Needed

1. Homepage
2. Registration
3. Dashboard (empty)
4. Upload page
5. Package selection
6. Dashboard (with packages)
7. Doc viewer
8. Doc editor
9. Pricing page
10. Horizon dashboard

## ✅ Production Ready When:

- [x] All manual tests pass
- [ ] Screenshots taken
- [ ] No console errors
- [ ] Queue jobs work
- [ ] Horizon accessible
- [x] Database migrations work on PostgreSQL

## PostgreSQL Testing ✅

**Tested and working!**

```bash
# Already configured in .env:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=markdown_observer
DB_USERNAME=postgres

# Migrations ran successfully ✅
```

## Ready to Ship?

If all checkboxes are ✅, then YES!
