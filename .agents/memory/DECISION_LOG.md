# 📜 Decision Log

## Architectural Decisions

### 1. Loop Engineering & Memory Bank Pattern
- **Decision**: Implemented a file-backed Memory Bank in `.agents/memory/` (`MEMORY.md`, `ACTIVE_CONTEXT.md`, `PROGRESS.md`, `DECISION_LOG.md`, `LESSONS_LEARNED.md`).
- **Rationale**: Ensures the AI maintains state across context limits and `/goal` autonomous loops, preventing context loss during long runs.

### 2. Standalone Release Builder Script
- **Decision**: Added `scripts/build-release.sh` using POSIX `/bin/sh` shebang and Alpine musl binding auto-resolution.
- **Rationale**: Allows 1-command release packaging both locally (`npm run release`, `composer build-zip`) and inside Docker (`docker compose run --rm app ./scripts/build-release.sh`).
