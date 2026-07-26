# Task Backlog for Loop Engineering

## Active Goal
Build and verify feature changes autonomously using the iterative loop.

### Task Checklist
- [ ] **Task 1**: Create a new API status endpoint at `GET /api/status` that returns JSON `{"status": "ok", "version": "1.0.0"}`.
- [ ] **Task 2**: Add a Pest feature test in `tests/Feature/ApiStatusTest.php` asserting that `GET /api/status` returns HTTP 200 and the correct JSON structure.
- [ ] **Task 3**: Run `./loop.sh` to ensure all Pest PHP tests and TestSprite verification tests pass 100% green.

---

## Instructions for AI Loop
1. Read the active task checklist in `PROMPT.md`.
2. Implement the requested code changes in the codebase.
3. Add or update unit & feature tests.
4. Execute `./loop.sh` to verify changes.
5. Fix any failures iteratively until `./loop.sh` succeeds and auto-commits the work.
