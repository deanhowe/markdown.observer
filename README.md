# Markdown.Observer

**The observability layer for your markdown — including the markdown your AI agents live on.**

Transform your Markdown files into a collaborative, version-controlled content system. Edit in Markdown or rich text, watch every change, and keep your files as the single source of truth — no lock-in, ever.

🌍 **Live:** [markdown.observer](https://markdown.observer) · **AI steering docs:** [ai.markdown.observer](https://ai.markdown.observer)

## What it does

### 📦 Package documentation, observed
- Upload a `composer.json` / `package.json` and sync docs for every dependency
- READMEs rendered with syntax highlighting (Shiki) and optimised logos
- Raw markdown + HTML endpoints for every file
- Version history per file — see what changed between releases

### 🤖 AI steering docs (the new frontier)
Every project now carries `.claude/`, `.kiro/`, `.ai/`, `.junie/` folders full of steering documents, rules, and skills — with no tooling to manage them. Markdown.Observer watches them:

- **Collections** organised by GitHub repo
- **Version history** for every steering doc — see how a project's agent rules evolve
- Hourly crawls tracking real movement across the ecosystem
- Live dashboard at [ai.markdown.observer](https://ai.markdown.observer)

### ✍️ Dual-mode editing
- Native Markdown or rich text (TipTap) — switch seamlessly
- Real-time Markdown ⇄ HTML ⇄ TipTap JSON conversion
- Full revision history on every page; files remain canonical

### 🛠 Built in public
- [Health dashboard](https://markdown.observer/health) on the live site
- API-first: REST endpoints for packages, pages, and conversions

## Pricing

**Free forever** tier, then Pro at **£9/month**, **£90/year**, or **£299 lifetime**. See [markdown.observer/pricing](https://markdown.observer/pricing).

## Stack

Laravel 12 · Inertia + React · TipTap · Tailwind 4 · Radix UI · Cashier (Stripe) · Horizon · PostgreSQL (production) / SQLite (local) · Deployed on Laravel Cloud

## Local development

Prerequisites: PHP 8.4, Composer, Node 24+.

```bash
git clone git@github.com:deanhowe/markdown.observer.git
cd markdown.observer

composer install
npm install

cp .env.example .env   # fill in the Stripe keys (see comments in the file)
php artisan key:generate

touch database/database.sqlite
php artisan migrate

composer dev           # dev server with hot reload (or: composer dev:ssr)
```

With [Herd](https://herd.laravel.com): the site resolves at `https://markdown.observer.test`, and the AI dashboard at `https://ai.markdown.observer.test`.

### Tests

```bash
php artisan test
```

## Docs

Deployment, environment, and feature notes live in [docs/](docs/). Historical session notes are archived in [docs/history/](docs/history/).

## License

Proprietary — © Dean Howe. Package code published under its own licenses where noted.
