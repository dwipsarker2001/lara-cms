---
name: release-management
description: Release packaging, version management in version.json, git tagging, and Docker zero-host-dependency ZIP generation.
---

# 📦 Release Management Skill

Activate this skill when bumping application versions or creating standalone distribution packages.

## Protocols

1. **Version Update**:
   - Update version string and download URL tag in `version.json`.
   - Create git tag `v<version>` (e.g. `git tag v1.3.5`).
2. **Release Build Execution**:
   - Local: `npm run release` or `composer build-zip` or `./scripts/build-release.sh`.
   - Docker (zero local host setup): `docker run --rm -v $(pwd):/app -w /app lara-cms-app ./scripts/build-release.sh`.
3. **Artifact Location**:
   - Output package is saved to `dist/lara-cms-v<version>.zip`.
