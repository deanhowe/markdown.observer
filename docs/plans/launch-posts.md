# Launch posts — markdown.observer

Drafts only — review before posting. Links assume the live production domain.

---

## Show HN

**Title:** Show HN: Markdown.Observer – an observability layer for your Markdown, including the Markdown your AI agents live on

**Body:**

Hi HN — I built Markdown.Observer, a small SaaS for tracking Markdown as a first-class artifact rather than a side effect of docs.

Two things it does:

1. **Package documentation, observed.** Upload a `composer.json` or `package.json`, it fetches the README for every dependency via the GitHub API, renders it with syntax highlighting, and keeps version history per file so you can see what changed between releases. Raw Markdown and HTML endpoints are exposed for every file.

2. **AI steering docs, organized.** Upload and organize agent instruction files in collections. GitHub crawling is still a preview: the hourly schedule and queue code exist, but a successful production run and reliable version history have not been verified. Do not publish an hourly-crawling claim until that verification is complete.

Editing is dual-mode — native Markdown or a TipTap rich-text view, converting live between Markdown, HTML, and TipTap JSON, with full revision history either way. The Markdown file stays canonical; there's no lock-in.

Stack: Laravel 13, PHP 8.5, Inertia + React, TipTap, Postgres in production, Stripe via Cashier, Horizon for queues, deployed on Laravel Cloud. The Laravel 13 revision is integrated locally; confirm publication and deployment before saying production runs Laravel 13.

Free tier is permanent (2 uploads, 10 packages). Pro is £9/mo or £90/yr for 100 packages; there's also a £299 lifetime option. Pricing: markdown.observer/pricing

Would love feedback, especially from anyone else trying to make sense of the growing pile of agent-steering Markdown their tools generate.

---

## X / Twitter thread

**1/**
Shipped Markdown.Observer: an observability layer for your Markdown — including the Markdown your AI agents actually live on.

markdown.observer

**2/**
Problem 1: your dependencies' docs live scattered across GitHub READMEs with no local history. Upload your composer.json/package.json, it fetches and renders every one, with version history per file.

**3/**
Problem 2: project agent instructions are scattered across .claude/, .kiro/, .ai/, and .junie/. Markdown.Observer lets you upload and organize those files. GitHub crawling is in preview; hourly production runs and complete version history still need verification.
→ ai.markdown.observer

**4/**
Edit in plain Markdown or rich text (TipTap) — converts live between Markdown ⇄ HTML ⇄ TipTap JSON. The .md file stays the source of truth either way. No lock-in.

**5/**
Free forever (2 uploads / 10 packages). Pro: £9/mo or £90/yr for 100 packages. Lifetime: £299.

Built with Laravel 13 + PHP 8.5 + Inertia/React + Postgres, deployed on Laravel Cloud. Confirm the published production revision before claiming Laravel 13 is live.

markdown.observer/pricing

---

## LinkedIn / longer-form

**Markdown.Observer is live.**

Markdown quietly became load-bearing infrastructure for software over the last two years — not just docs, but the actual rulebook your AI coding agents operate from (.claude/, .kiro/, .ai/, .junie/ folders are now standard in most repos). Almost nobody versions or watches that layer.

Markdown.Observer does two things:

→ Tracks your dependencies' documentation. Upload a composer.json or package.json, it pulls the README for every package via GitHub, renders it with syntax highlighting, and keeps a version history per file.

→ Organizes uploaded AI steering docs by collection. GitHub crawling is in preview; verify a successful production run and history before making stronger claims.

Everything stays Markdown. Edit natively or in a rich-text view (TipTap) with live conversion between Markdown, HTML, and TipTap JSON — your files remain the canonical source, so there's no lock-in to the platform.

Free tier is permanent. Pro is £9/month (or £299 one-time for lifetime access).

Built on Laravel 13 + PHP 8.5 + Inertia/React, deployed on Laravel Cloud. Confirm the published production revision before claiming Laravel 13 is live.

markdown.observer
