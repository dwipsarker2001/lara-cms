# 🧠 Project Memory Bank (Lara-CMS)

## 📌 Project Overview
- **Name**: Lara-CMS
- **Framework**: Laravel 13, PHP 8.4+, Alpine.js v3, Tailwind CSS v3, Docker
- **Architecture**: Schema-driven, block-based Content Management System
- **Key Directory Structure**:
  - `app/Blocks/custom/` — Block PHP schema definitions
  - `resources/views/blocks/custom/` — Blade HTML view templates for blocks
  - `app/Models/` — Collection, CollectionEntry, PageView, Layout, Form, FormEntry, Asset, Taxonomy, Term, User, Admin
  - `scripts/build-release.sh` — Automated standalone production ZIP packager

## 📐 Conventions & Rules
- **PHP Code Style**: Always format modified PHP files with `vendor/bin/pint --dirty --format agent`.
- **Testing Enforcement**: Always run tests with `php artisan test --compact` to verify functionality.
- **Visual Editor Attributes**: Bind `data-block`, `data-edit`, and `data-list` attributes in Blade templates.
- **Docker Environment**: Docker image `lara-cms-app` includes PHP 8.4, Composer, Node.js, NPM, and Zip.

## 🔄 Autonomous Loop Protocol (/goal)
When executing `/goal` tasks:
1. **Load Context**: Read `.agents/memory/ACTIVE_CONTEXT.md` and `.agents/memory/MEMORY.md`.
2. **Execute Iteratively**: Work step-by-step, running pint and pest tests on every change.
3. **Persist State**: Update `.agents/memory/PROGRESS.md` and `.agents/memory/ACTIVE_CONTEXT.md`.
4. **Audit**: Do not output `<!-- GOAL_COMPLETE -->` until all empirical test verifications pass.
