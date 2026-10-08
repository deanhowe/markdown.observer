# Markdown.Observer — Laravel 13 upgrade

Implemented locally on 8 October 2026. The upgrade is integrated into the
canonical checkout; GitHub publication and production deployment remain
pending.

## Changes

- Laravel 12.50.0 → 13.35.0; Tinker 3; Pest 4 and PHPUnit 12.
- Updated compatible dependency releases, including Cashier 16.8.0.
- PHP ^8.5 with Composer platform 8.5.0, following Dean’s instruction to use the latest stable PHP branch.
- Updated request-forgery configuration, preserving the Stripe webhook exemption.
- Homepage badge now reads “Built with Laravel, React & TipTap”. README reflects Laravel 13.
- Added regression coverage for main/AI domain routing and webhook exemption versus protected checkout.
- Existing cache prefixes and session configuration retained. No schema changes.

## Validation

- Portable Composer resolution used isolated COMPOSER_HOME; no Forge hostname in manifest or lock.
- Composer validation passed; Composer reported no security vulnerability advisories.
- Full PHP 8.4 suite during the isolated upgrade: 123 passed, 483 assertions,
  12 risky, 6 skipped; no failures.
- Risky cases are pre-existing early-return placeholders in MarkdownService and PageService tests.
- Skips include explicitly disabled settings tests and an unavailable vendor fixture.
- npm ci and production Vite build passed.
- Git fsck found no corrupt objects; existing dangling objects preserved.
- Final PHP 8.5.0 suite: 123 passed, 483 assertions, 12 existing risky placeholders, 6 existing skips; no failures or deprecation warnings.
- PHP 8.5 platform/extension requirements passed.
- The Cloud environment was reported as PHP 8.5 / Node 24 with push-to-deploy
  enabled during upgrade preparation; the live deployment has not been
  re-verified since this revision was integrated.
- Replaced deprecated PDO MySQL SSL constants with Pdo\Mysql::ATTR_SSL_CA.

## Ownership and preservation

Upgrade commit: `29fb13f`; PHP 8.5 follow-up: `f342fa5`; local frontend and
claim fixes: `cab44d5` on `main`.
Canonical checkout: `sites/markdown.observer`. The Laravel 13 runtime is
installed from `composer.lock`; the old vendor tree was generated dependency
state, not unique Git history.
The previous isolated-worktree note is historical. Current local changes are
tracked in the canonical checkout and its `.kiro` Native Git Unit.
MoofForge publication is allowed by the project contract. GitHub publication
requires Dean’s approval of the exact commit and `main` ref.

## Production follow-up

After approved GitHub publication, verify the deployed revision and homepage,
Cloud PHP >=8.5, checkout/webhooks, and queue workers. Browser payment
completion and a successful production crawler run are still unverified.
