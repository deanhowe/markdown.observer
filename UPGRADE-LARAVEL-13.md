# Markdown.Observer — Laravel 13 upgrade

Prepared 7 October 2026 in an isolated Codex worktree.

## Changes

- Laravel 12.50.0 → 13.35.0; Tinker 3; Pest 4 and PHPUnit 12.
- Updated compatible dependency releases, including Cashier 16.8.0.
- PHP ^8.3 with Composer platform 8.3.0, preventing accidental resolution to PHP 8.4-only packages.
- Updated request-forgery configuration, preserving the Stripe webhook exemption.
- Homepage badge now reads “Built with Laravel, React & TipTap”. README reflects Laravel 13.
- Added regression coverage for main/AI domain routing and webhook exemption versus protected checkout.
- Existing cache prefixes and session configuration retained. No schema changes.

## Validation

- Portable Composer resolution used isolated COMPOSER_HOME; no Forge hostname in manifest or lock.
- Composer validation passed; Composer reported no security vulnerability advisories.
- Full PHP 8.4 suite: 123 passed, 483 assertions, 12 risky, 6 skipped; no failures.
- Risky cases are pre-existing early-return placeholders in MarkdownService and PageService tests.
- Skips include explicitly disabled settings tests and an unavailable vendor fixture.
- npm ci and production Vite build passed.
- Git fsck found no corrupt objects; existing dangling objects preserved.
- PHP 8.3 verification is recorded in the final delivery report.

## Ownership and preservation

Baseline: cffdf9e8125b099386a4448b15772a42ebed53f3.
Branch: codex/markdown-laravel-13.
Canonical checkout: sites/markdown.observer; isolated worktree retained.
Existing dirty package files, settings layout, launch-post draft and legacy metadata deletion excluded.
Exact scope: composer.json, composer.lock, bootstrap/app.php, README.md, resources/js/pages/Welcome.tsx, tests/Feature/Laravel13CompatibilityTest.php, this report.
Commit and verified MoofForge branch publication authorized. GitHub publication requires Dean’s approval of the exact commit and main ref.
Return strategy: reviewed commit. Retention: preserve.

## Production follow-up

Verify the deployed revision and homepage after approved GitHub publication. Confirm Cloud PHP >=8.3, checkout/webhooks and queue workers. Browser payment completion and crawler execution were not performed as part of this isolated upgrade.
