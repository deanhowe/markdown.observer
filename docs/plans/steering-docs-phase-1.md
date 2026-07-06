# Steering Docs — Phase 1 (grounded plan, 2026-07-06)

Supersedes the Phase 1 checklist in the original STEERING_DOCS_FEATURE.md vision
doc (now in docs/history/). That doc predates the code — much of "Phase 1"
already exists. This plan starts from what is actually built.

## Already built (verified in code)

- `POST /steering/upload` — `SteeringDocController@upload`: multi-file upload,
  10MB/file, tier limits (free=1 collection, pro=10, lifetime=∞)
- Models + relations: `SteeringCollection → SteeringDoc → SteeringDocVersion`
- Folder-type detection (`claude`, `kiro`, fallback `ai`)
- `ai.markdown.observer` subdomain with stats dashboard (`AI\HomeController`,
  `AiSteering/Welcome.tsx`)
- Hourly GitHub crawls with version tracking (see git log: "hourly crawls to
  measure real movement")

## Phase 1 gaps — ship these next

### 1. Fix folder detection for 2026 conventions (small, high value)
`detectFolderType()` looks for `instructions.md`/`settings.json` (old Claude
convention) and misses today's layout. Update to recognise:
- `.claude/`: `CLAUDE.md` (root or in-folder), `skills/*/SKILL.md`, `commands/*.md`, `agents/*.md`, `settings.json`
- `.kiro/`: `steering/*.md`, `prompts/*.md`, `mcp.json`
- `.cursor/rules/`, `.cursorrules`, `AGENTS.md` (cursor / generic)
- `.junie/`: `guidelines.md`, `mcp/mcp.json`
- `.aider.conf.yml`, `.windsurf/` (validation already accepts these types but
  detection never returns them)
File: `app/Http/Controllers/SteeringDocController.php`. Add a unit test per
convention using fixture folders — this repo's own `.kiro/` + `.junie/` dirs
are real fixtures.

### 2. Versioning on re-upload (the actual product promise)
Upload currently always creates a new collection; `SteeringDocVersion` is
never written from the upload path. Change: when a user re-uploads to an
existing collection (match by name/repo), diff per `file_path` — unchanged
files no-op, changed files snapshot current content into
`steering_doc_versions` then update. This is the "observer" moment: the diff
timeline is the feature.

### 3. Minimum viable editing/viewing UI
`AiSteering/` has only `Welcome.tsx`. Add: collection list → doc list → doc
view with version history. Reuse the existing TipTap `PageEditor.tsx` wiring
and `PageRevision` display patterns rather than building new components.

### 4. Export (closes the loop)
"Download collection as folder" (zip with original paths). Users must trust
they can leave — no-lock-in is the brand.

## Explicitly NOT Phase 1
Cross-project sync, format conversion between agent conventions, public
collection sharing, team features.

## Verification
- Unit: folder detection per convention (fixtures above)
- Feature: upload → re-upload modified file → assert `SteeringDocVersion`
  row created and doc content updated
- Manual: upload this repo's own `.kiro/` folder at
  `https://ai.markdown.observer.test`, edit a doc, re-upload, check history
