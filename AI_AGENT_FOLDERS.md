# AI Agent Folder Conventions

Research findings from top open-source projects (Feb 2026).

## Confirmed Patterns

### `.claude/` (Claude AI)
**Found in:** React, Next.js, Livewire

**Structure:**
```
.claude/
├── instructions.md    # Main steering document
├── settings.json      # Agent configuration
├── rules/            # Project-specific rules
├── skills/           # Reusable skills/commands
└── commands/         # Custom slash commands
```

**Example (React):**
- Project structure documentation
- Key packages and their purposes
- Requirements (Node, package manager)
- Custom commands (`/verify`, `/fix`, `/test`, `/flow`)
- Build instructions

### `.cursor/` (Cursor IDE)
**Found in:** Next.js

**Structure:**
```
.cursor/
├── commands/         # Custom commands
└── worktrees.json   # Workspace configuration
```

## Known Agent Folders (Not Yet Confirmed)

Based on AI coding assistant ecosystem:

- `.ai/` - Generic AI agent folder (moof.one uses this)
- `.kiro/` - Kiro CLI (AWS)
- `.aider/` - Aider AI pair programmer
- `.windsurf/` - Windsurf IDE
- `.continue/` - Continue.dev
- `.codeium/` - Codeium
- `.tabnine/` - TabNine
- `.github/copilot/` - GitHub Copilot
- `.amazon-q/` - Amazon Q
- `.sourcegraph/` - Sourcegraph Cody
- `.replit/` - Replit AI
- `.gitpod/` - Gitpod

## Common Patterns

### File Types
1. **Instructions/Rules** - Main steering document
2. **Settings/Config** - Agent configuration (JSON/YAML)
3. **Skills** - Reusable commands/patterns
4. **Commands** - Custom slash commands
5. **Context** - Project-specific context

### Content Structure
- Project overview
- Directory structure
- Key concepts/patterns
- Requirements and constraints
- Custom commands
- Testing instructions
- Build/deploy process

## Use Cases

### 1. Package Documentation (Current)
Upload composer.json/package.json → fetch docs → edit → sync

### 2. Steering Docs (Future)
Upload .claude/.ai/.kiro folders → sync across projects → keep agents aligned

### 3. Multi-Agent Coordination
Write once → export to multiple formats → all agents stay in sync

## Product Opportunity

**"Steering Doc Hub"** - Central place to:
- Write steering docs once
- Export to multiple formats (.claude, .cursor, .kiro, .ai, etc.)
- Sync across projects
- Browse public steering docs (opt-in)
- Learn from best practices (React, Next.js, Livewire)

## Demo Files Available

**Steering docs from:**
- facebook/react (.claude/)
- vercel/next.js (.claude/ + .cursor/)
- livewire/livewire (.claude/)

**Package files (66 total):**
- 19 composer.json (PHP projects)
- 26 package.json (JS projects)
- 21 both formats

---

*Research Date: 2026-02-07*
*Projects Analyzed: 26*
*Confirmed Patterns: 3 (.claude, .cursor, .ai)*
