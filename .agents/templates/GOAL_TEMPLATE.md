# 🎯 Autonomous Goal Plan Template

## Goal Overview
- **Goal Title**: [Short title describing the objective]
- **Target Component / Area**: [e.g. app/Blocks/custom, Admin Collections, SEO Pro]
- **Success Criteria**:
  - [ ] Criterion 1
  - [ ] Criterion 2
  - [ ] All unit & feature tests passing (`php artisan test --compact`)

---

## 🛠️ Step-by-Step Task Breakdown

### Phase 1: Research & Schema Inspection
- [ ] Inspect existing sibling classes and database schema
- [ ] Document entrypoints and API contracts

### Phase 2: Implementation & Formatting
- [ ] Implement core logic / migrations / controllers / views
- [ ] Format PHP files (`vendor/bin/pint --dirty --format agent`)

### Phase 3: Automated Testing & Verification
- [ ] Write/update Pest tests for new functionality
- [ ] Run full health check (`./scripts/loop-engine.sh`)

### Phase 4: Memory Bank Synchronization
- [ ] Update `.agents/memory/ACTIVE_CONTEXT.md`
- [ ] Update `.agents/memory/PROGRESS.md`
- [ ] Log architectural decisions in `.agents/memory/DECISION_LOG.md`
