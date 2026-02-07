# Manual Testing Guide

## Prerequisites

1. Start dev server:
```bash
cd ~/PLANNR/VALET/VHOSTS/markdown.observer
php artisan serve --port=8001
```

2. Visit: http://127.0.0.1:8001

## Test 1: Registration & Login

1. Click "Get Started" or "Register"
2. Fill in:
   - Name: Test User
   - Email: test@example.com
   - Password: password
3. Submit
4. Should redirect to dashboard
5. ✅ Check: User is logged in

## Test 2: Upload composer.json

1. From dashboard, click "Upload Packages"
2. Upload this project's `composer.json` file
3. ✅ Check: See list of packages (should show ~19 packages)
4. ✅ Check: Packages like laravel/framework, spatie/laravel-data visible
5. ✅ Check: PHP and ext-* packages filtered out

## Test 3: Select Packages

1. Select 3-5 packages (stay under free tier limit of 10)
2. Click "Add X Packages"
3. ✅ Check: Redirects to dashboard
4. ✅ Check: Success message shown
5. ✅ Check: Packages listed on dashboard

## Test 4: Queue Processing

1. In terminal, run:
```bash
php artisan queue:work
```

2. Watch jobs process
3. ✅ Check: Jobs complete without errors
4. Refresh dashboard
5. ✅ Check: Doc counts update

## Test 5: View Docs

1. Click on a package (e.g., "laravel/framework")
2. ✅ Check: Docs page loads
3. ✅ Check: Sidebar shows doc files
4. ✅ Check: README.md content displays
5. ✅ Check: Can click different docs

## Test 6: Edit Docs

1. On docs page, click "Edit" button
2. Change some text
3. Click "Save"
4. ✅ Check: Success message
5. ✅ Check: Changes visible
6. ✅ Check: Orange asterisk (*) appears next to filename

## Test 7: Sync Package

1. Go back to dashboard
2. Click "Sync" button on a package
3. ✅ Check: "Sync queued!" message
4. Run queue worker
5. ✅ Check: Docs update

## Test 8: Limits

1. Try to upload more packages than limit allows
2. ✅ Check: Error message about limit
3. ✅ Check: Can't exceed 10 packages on free tier

## Test 9: Pricing Page

1. Visit /pricing
2. ✅ Check: Three tiers shown (Free, Pro, Lifetime)
3. ✅ Check: Features listed
4. ✅ Check: Prices displayed

## Test 10: Horizon

1. Visit /horizon
2. ✅ Check: Dashboard loads
3. ✅ Check: Can see completed jobs
4. ✅ Check: Metrics displayed

## Quick Test Script

```bash
# 1. Register user
curl -X POST http://127.0.0.1:8001/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@test.com","password":"password","password_confirmation":"password"}'

# 2. Login
curl -X POST http://127.0.0.1:8001/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password"}' \
  -c cookies.txt

# 3. Upload composer.json
curl -X POST http://127.0.0.1:8001/upload \
  -b cookies.txt \
  -F "file=@composer.json"

# 4. View dashboard
curl http://127.0.0.1:8001/dashboard -b cookies.txt
```

## Expected Results

- ✅ All pages load without errors
- ✅ Can register and login
- ✅ Can upload and parse composer.json
- ✅ Can select packages
- ✅ Queue jobs process
- ✅ Can view docs
- ✅ Can edit docs
- ✅ Can sync packages
- ✅ Limits enforced
- ✅ Horizon works

## If Something Fails

1. Check logs: `tail -f storage/logs/laravel.log`
2. Check queue: `php artisan queue:failed`
3. Check database: `php artisan db:show`
4. Check routes: `php artisan route:list`

## Ready to Ship?

If all 10 tests pass, you're good to deploy!
