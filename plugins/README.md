# Plugins Directory

This directory contains **plugins** for Lara CMS. Plugins are isolated tools or integrations
that you build yourself and are **completely protected from CMS core updates**.

## How it works

When you run a CMS update, the `plugins/` directory is **never touched or modified**.
Your custom tools live here permanently.

## Creating a New Plugin

1. Create a folder inside `plugins/` with your plugin's slug (e.g., `plugins/newsletter/`)
2. Add a `plugin.json` manifest file:

```json
{
    "name": "Newsletter",
    "slug": "newsletter",
    "version": "1.0.0",
    "description": "Newsletter subscription management.",
    "author": "Your Name",
    "requires_cms": "1.0.0"
}
```

3. Optionally add:
   - `routes/admin.php` → Admin panel routes (auto-loaded under `/admin` with `auth:admin`)
   - `routes.php` → Public web routes
   - `routes/api.php` → API routes (loaded under `/api`)
   - `views/` → Blade views (namespaced as `your-plugin-slug::view-name`)
   - `src/` → PHP classes

## Example plugins

- `email-marketing/` — Example email marketing tool
