---
name: block-development
description: Guide and patterns for defining schema-driven PHP Block classes and interactive Blade templates in Lara-CMS.
---

# 🧱 Block Development Skill

Activate this skill when creating or modifying custom page blocks or global header/footer components.

## Conventions

1. **PHP Schema Class (`app/Blocks/custom/Name.php`)**:
   - Extends `App\Blocks\Block`.
   - Defines `$name` (camelCase) and `$label`.
   - Set `public bool $global = true;` for global site headers/footers.
   - Use `Field::string()`, `Field::image()`, `Field::icon()`, `Field::list()`, `Field::group()`.

2. **Blade Template View (`resources/views/blocks/custom/kebab-name.blade.php`)**:
   - Root container must have `data-block="camelCaseName"`.
   - Bind `data-edit="fieldKey"` and `data-list="listKey"` for visual page editor integration.
   - Support background overlay (`$data['background']`) when enabled.
