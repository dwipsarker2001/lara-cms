---
name: laravel-best-practices
description: Industry-standard Laravel 13 development, Pest 4 testing, Pint code formatting, and Boost MCP query conventions.
---

# ⚡ Laravel Best Practices Skill

Activate this skill when creating or modifying Laravel models, controllers, migrations, services, or Pest test suites.

## Guidelines

1. **Eloquent & Schemas**:
   - Always check database tables using Boost tools or migrations before modifying models.
   - Use PHP 8 constructor property promotion and explicit return type declarations.
2. **Pest Testing**:
   - Every feature or logic update MUST be accompanied by Pest tests.
   - Execute tests with `php artisan test --compact` or filter with `--filter=TestName`.
3. **Code Style**:
   - Format dirty PHP files with `vendor/bin/pint --dirty --format agent`.
4. **Artisan Commands**:
   - Pass `--no-interaction` to Artisan commands.
