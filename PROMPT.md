# Task Backlog for Loop Engineering

## Active Goal
Build and verify feature changes autonomously using the iterative loop with meaningful Git commit messages.

---

## Instructions for AI Loop
1. Read the active task checklist in `PROMPT.md`.
2. Implement the requested code changes in the codebase.
3. Add or update unit & feature tests.
4. Execute `./loop.sh "<descriptive commit message>"` (e.g. `./loop.sh "fix(auth): update login route and default admin email"`).
5. Fix any failures iteratively until `./loop.sh` succeeds and auto-commits the specific descriptive message.
