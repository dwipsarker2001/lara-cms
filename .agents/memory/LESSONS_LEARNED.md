# 💡 Lessons Learned & Troubleshooting

## Learned Patterns & Fixes

1. **Alpine Linux Musl Binding for Vite / Rolldown**:
   - **Issue**: Vite 8 / Rolldown requires `@rolldown/binding-linux-x64-musl` on Alpine Linux (Docker), which differs from glibc Linux host (`@rolldown/binding-linux-x64-gnu`).
   - **Fix**: In `scripts/build-release.sh`, check if `/etc/alpine-release` exists and auto-install `@rolldown/binding-linux-x64-musl` before building.

2. **Composer Exec String Escaping in JSON**:
   - **Issue**: Escaping double quotes inside double quotes in `composer.json` script arrays causes PHP parse errors.
   - **Fix**: Use clean dedicated shell scripts (`scripts/build-release.sh`) invoked directly by `composer.json` and `package.json`.
