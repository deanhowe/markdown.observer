# Deployment Guide for Markdown.Observer

This document provides instructions for deploying Markdown.Observer to Laravel Cloud.

## Prerequisites

Before deploying, ensure you have:

1. A Laravel Cloud account (https://laravel.com/cloud)
2. Access to the GitHub repository
3. A Sentry account for error tracking (https://sentry.io)

## Deployment Process

### 1. Connect GitHub Repository to Laravel Cloud

1. Log in to your Laravel Cloud account
2. Create a new application
3. Select "GitHub" as the source
4. Select the Markdown.Observer repository
5. Configure the branch to deploy (usually `main`)

### 2. Configure Environment Variables

The following environment variables should be set in the Laravel Cloud dashboard:

#### Essential Variables
- `APP_KEY`: Generate using `php artisan key:generate --show`
- `APP_URL`: Your production URL (e.g., https://markdown.observer)
- `APP_DEBUG`: Set to `false` in production

#### Database Configuration
Laravel Cloud will automatically configure the database connection, but you can customize if needed:
- `DB_CONNECTION`: Usually set to `mysql` by default
- `DB_HOST`: Automatically set by Laravel Cloud
- `DB_PORT`: Automatically set by Laravel Cloud
- `DB_DATABASE`: Automatically set by Laravel Cloud
- `DB_USERNAME`: Automatically set by Laravel Cloud
- `DB_PASSWORD`: Automatically set by Laravel Cloud

#### Mail Configuration
- `MAIL_MAILER`: Set to `smtp` for production
- `MAIL_HOST`: Your mail server host
- `MAIL_PORT`: Your mail server port (typically 587 for TLS)
- `MAIL_USERNAME`: Your mail server username
- `MAIL_PASSWORD`: Your mail server password
- `MAIL_ENCRYPTION`: Set to `tls` for most providers
- `MAIL_FROM_ADDRESS`: The email address to send from (e.g., hello@markdown.observer)
- `MAIL_FROM_NAME`: The name to display in emails (e.g., "Markdown Observer")

#### Error Tracking
- `SENTRY_LARAVEL_DSN`: Your Sentry DSN from the Sentry dashboard
- `SENTRY_TRACES_SAMPLE_RATE`: Set to `0.1` for 10% of transactions to be sent to Sentry

### 3. Deploy the Application

1. Once the repository is connected and environment variables are set, Laravel Cloud will automatically deploy the application when changes are pushed to the configured branch.
2. You can also manually trigger a deployment from the Laravel Cloud dashboard.

### 4. Post-Deployment Steps

After successful deployment:

1. Run database migrations:
   ```
   php artisan migrate --force
   ```

2. Clear caches:
   ```
   php artisan optimize:clear
   php artisan optimize
   ```

3. Verify the application is working correctly by visiting the URL.

### 5. Monitoring and Maintenance

#### Monitoring

1. **Laravel Cloud Dashboard**: Monitor application health, logs, and performance.
2. **Sentry Dashboard**: Monitor errors and exceptions.

#### Regular Maintenance

1. Keep dependencies updated:
   ```
   composer update
   npm update
   ```

2. Review logs regularly for errors or performance issues.

3. Set up regular database backups (Laravel Cloud handles this automatically).

## Rollback Procedure

If issues occur after deployment:

1. From the Laravel Cloud dashboard, select a previous deployment to roll back to.
2. Monitor the application after rollback to ensure it's functioning correctly.
3. Investigate the issue that caused the need for rollback.

## Continuous Integration/Deployment

The repository includes GitHub Actions workflows for CI/CD:

1. **Linting**: Runs on every push to ensure code quality.
2. **Tests**: Runs the test suite to ensure functionality.
3. **Deployment**: Automatically deploys to Laravel Cloud when changes are pushed to the main branch.

To view the status of these workflows, check the "Actions" tab in the GitHub repository.

## Troubleshooting

### Common Issues

1. **Database Connection Issues**:
   - Verify database credentials in Laravel Cloud environment variables.
   - Check if the database server is accessible from Laravel Cloud.

2. **Asset Loading Issues**:
   - Ensure assets are properly built before deployment with `npm run build`.
   - Check for JavaScript console errors.

3. **Error Tracking Not Working**:
   - Verify the Sentry DSN is correctly set in environment variables.
   - Check Sentry documentation for Laravel integration issues.

For additional help, contact the development team or refer to Laravel Cloud documentation.
