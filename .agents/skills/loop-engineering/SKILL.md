---
name: loop-engineering
description: Autonomous goal-loop execution protocol with persistent memory bank synchronization and health check verification for /goal tasks.
---

# 🔄 Loop Engineering Skill

Activate this skill when executing `/goal` tasks or long-running autonomous development loops.

## Core Directives

1. **Load Memory Bank**:
   - Inspect `.agents/memory/ACTIVE_CONTEXT.md` and `.agents/memory/MEMORY.md`.
2. **Goal Plan Decomposition**:
   - Fill out `.agents/templates/GOAL_TEMPLATE.md` to breakdown tasks into verifiable sub-tasks.
3. **Execute & Verify**:
   - After code edits, execute `./scripts/loop-engine.sh` to auto-format PHP files (`pint`) and run Pest tests (`php artisan test --compact`).
4. **Synchronize Memory**:
   - Update `.agents/memory/PROGRESS.md` and `.agents/memory/DECISION_LOG.md`.
5. **Completion Audit**:
   - Do NOT emit `<!-- GOAL_COMPLETE -->` until all test verifications pass cleanly.
