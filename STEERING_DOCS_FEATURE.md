# Steering Docs Feature

## Vision

Expand markdown.observer to support AI agent steering documents alongside package documentation.

## Current State
- ✅ Upload composer.json/package.json
- ✅ Parse dependencies
- ✅ Fetch package docs from GitHub
- ✅ Edit and sync docs

## Proposed Addition
- Upload `.claude/`, `.ai/`, `.kiro/` folders
- Parse steering docs structure
- Edit with rich text editor
- Export to multiple formats
- Sync across projects

## Implementation Plan

### Phase 1: Support Uploading Folders
- [ ] Add folder upload to UploadPackages page
- [ ] Detect folder type (.claude, .ai, .kiro, etc.)
- [ ] Parse folder structure (instructions.md, rules/, skills/)
- [ ] Store in database (new table: `steering_docs`)

### Phase 2: Editor
- [ ] View steering docs in dashboard
- [ ] Edit with TipTap (markdown ↔ rich text)
- [ ] Save changes
- [ ] Version history

### Phase 3: Multi-Format Export
- [ ] Export to .claude/ format
- [ ] Export to .cursor/ format
- [ ] Export to .kiro/ format
- [ ] Export to .ai/ format
- [ ] Download as ZIP

### Phase 4: Public Gallery (Opt-in)
- [ ] Browse public steering docs
- [ ] Fork/clone steering docs
- [ ] Learn from React, Next.js, Livewire patterns
- [ ] Template library

## Database Schema

```sql
-- Steering doc collections
CREATE TABLE steering_collections (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    name VARCHAR(255),
    type VARCHAR(50), -- 'claude', 'cursor', 'kiro', 'ai'
    is_public BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Individual steering docs
CREATE TABLE steering_docs (
    id BIGINT PRIMARY KEY,
    collection_id BIGINT,
    file_path VARCHAR(500), -- 'instructions.md', 'rules/testing.md'
    content TEXT,
    is_edited BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Revenue Impact

### Current Tiers
- Free: 2 uploads, 10 packages
- Pro: £9/mo, 100 packages
- Lifetime: £299, unlimited

### With Steering Docs
- Free: 2 uploads, 10 packages, 1 steering collection
- Pro: £9/mo, 100 packages, 10 steering collections
- Lifetime: £299, unlimited everything

## Competitive Advantage

**No one else is doing this.**

- Package docs: Niche but useful
- Steering docs: **Game changer for multi-project devs**
- Combined: Unique value proposition

## Target Users

1. **Solo devs with multiple projects** (Dean's use case)
2. **Teams standardizing AI agent behavior**
3. **Open source maintainers** (React, Next.js already doing this)
4. **Agencies managing client projects**

## Next Steps

1. ✅ Ship animated blobs
2. ✅ Document research findings
3. ⏳ Add folder upload support
4. ⏳ Build steering doc editor
5. ⏳ Multi-format export

---

*Status: Research Complete*
*Next: Implement Phase 1*
