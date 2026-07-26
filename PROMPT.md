# Task Backlog for Loop Engineering

## Active Task
Define the task or feature you want the AI agent to implement autonomously.

- [ ] Task 1: Specify feature description here
- [ ] Task 2: Specify acceptance criteria / tests

---

## Instructions for AI Loop
1. Read the active task in `PROMPT.md`.
2. Implement the required code changes in the codebase.
3. Write or update tests in `tests/Feature/` or `testsprite-tests/`.
4. Execute `./loop.sh` to verify that both Pest PHP tests and TestSprite cloud tests pass.
5. If any test fails, analyze the error output and iteratively fix the code until `./loop.sh` reports `SUCCESS!`.
