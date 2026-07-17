# Graph Report - lara-cms  (2026-07-17)

## Corpus Check
- 288 files · ~122,339 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1380 nodes · 1718 edges · 238 communities (192 shown, 46 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 87 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `34b5730b`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- dependencies
- Layout
- composer.json
- Page
- Controller
- Post
- scripts
- Model
- LoginController.php
- Taxonomy
- Field
- Block
- command
- BlockRegistry
- tiptap.js
- Field.php
- AppServiceProvider.php
- TestCase.php
- BlogPostSlot.php
- ClientTestimonials.php
- DestinationsGrid.php
- FeatureImageCards.php
- HeroBanner.php
- LatestBlog.php
- PackageList.php
- PackagePostSlot.php
- PageBanner.php
- Contact.php
- TeamCards.php
- TravelDeals.php
- WhyChooseUs.php
- SiteFooter.php
- SiteNavbar.php
- SiteTopBar.php
- laravel-boost
- graphify.js
- docker-entrypoint.sh
- package-post-slot.blade.php
- Seeder
- opencode.json
- ProfileBento.php
- package-about.blade.php
- package-booking.blade.php
- package-details.blade.php
- package-faq.blade.php
- package-features.blade.php
- package-gallery-hero.blade.php
- package-hero.blade.php
- package-highlights.blade.php
- package-info.blade.php
- package-itinerary.blade.php
- package-locations.blade.php
- package-map.blade.php
- RedirectResponse
- View
- Lara-CMS — Full Rebuild Specification
- .view
- Package
- Tailwind CSS Development
- Layout
- Security Best Practices
- Tailwind CSS Development
- Architecture Best Practices
- Queue & Job Best Practices
- Security Best Practices
- Architecture Best Practices
- 4. Data model & migrations
- Advanced Query Patterns
- Database Performance Best Practices
- Events & Notifications Best Practices
- Advanced Query Patterns
- Database Performance Best Practices
- Events & Notifications Best Practices
- Queue & Job Best Practices
- Docker MySQL + phpMyAdmin Setup
- Caching Best Practices
- Eloquent Best Practices
- Migration Best Practices
- Caching Best Practices
- Eloquent Best Practices
- Migration Best Practices
- Layouts Collection Implementation Plan
- SKILL.md
- Blade & Views Best Practices
- Error Handling Best Practices
- Task Scheduling Best Practices
- Testing Best Practices
- SKILL.md
- Blade & Views Best Practices
- Error Handling Best Practices
- Task Scheduling Best Practices
- Testing Best Practices
- 10. The Visual Editor (the crown jewel)
- Collection Best Practices
- HTTP Client Best Practices
- Mail Best Practices
- Routing & Controllers Best Practices
- Conventions & Style
- Validation & Forms Best Practices
- Collection Best Practices
- HTTP Client Best Practices
- Mail Best Practices
- Routing & Controllers Best Practices
- Conventions & Style
- Validation & Forms Best Practices
- Appendix A — full block catalog
- Configuration Best Practices
- Configuration Best Practices
- 13. Other collections: Blog, Packages, Bookings
- 5. The block system (the engine)
- 7. Public website rendering
- 16. Routes reference
- 8. Admin panel — shell, nav, layout
- BlogList.php
- BlogSection.php
- PackageFaq.php
- PackageFeatures.php
- PackageGalleryHero.php
- PackageHero.php
- PackageInfo.php
- PackageLocations.php
- PackageMap.php
- SimpleText.php
- TravelDeals.php
- 9. Pages CRUD
- Sections
- SeoController.php
- Model
- Term.php
- ProfileController.php
- Request

## God Nodes (most connected - your core abstractions)
1. `Block` - 73 edges
2. `Field` - 49 edges
3. `Controller` - 33 edges
4. `Layout` - 27 edges
5. `Page` - 26 edges
6. `Post` - 25 edges
7. `Admin` - 24 edges
8. `Form` - 21 edges
9. `Sections` - 21 edges
10. `Lara-CMS — Full Rebuild Specification` - 20 edges

## Surprising Connections (you probably didn't know these)
- `AboutIntro` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/AboutIntro.php → app/Blocks/Block.php
- `BlogList` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/BlogList.php → app/Blocks/Block.php
- `BlogPostSlot` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/BlogPostSlot.php → app/Blocks/Block.php
- `BlogSection` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/BlogSection.php → app/Blocks/Block.php
- `ClientTestimonials` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/ClientTestimonials.php → app/Blocks/Block.php

## Import Cycles
- None detected.

## Communities (238 total, 46 thin omitted)

### Community 0 - "dependencies"
Cohesion: 0.04
Nodes (47): alpinejs, autoprefixer, concurrently, @fortawesome/fontawesome-free, laravel-vite-plugin, dependencies, alpinejs, sortablejs (+39 more)

### Community 1 - "Layout"
Cohesion: 0.07
Nodes (26): APIs & Eloquent Resources, Application Structure & Architecture, Artisan, Conventions, Deployment, Do Things the Laravel Way, Documentation Files, Foundational Context (+18 more)

### Community 2 - "composer.json"
Cohesion: 0.05
Nodes (42): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+34 more)

### Community 3 - "Page"
Cohesion: 0.19
Nodes (4): PageController, Request, PageController, Page

### Community 4 - "Controller"
Cohesion: 0.16
Nodes (4): CommandSearchController, Request, CommandSearch, JsonResponse

### Community 5 - "Post"
Cohesion: 0.07
Nodes (27): APIs & Eloquent Resources, Application Structure & Architecture, Artisan, Conventions, Deployment, Do Things the Laravel Way, Documentation Files, Foundational Context (+19 more)

### Community 6 - "scripts"
Cohesion: 0.08
Nodes (27): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+19 more)

### Community 7 - "Model"
Cohesion: 0.11
Nodes (8): AssetsController, Request, Request, SeoController, Request, SettingsController, Asset, Setting

### Community 8 - "LoginController.php"
Cohesion: 0.11
Nodes (10): RedirectResponse, View, ProfileController, LoginController, RedirectResponse, Request, View, LoginRequest (+2 more)

### Community 9 - "Taxonomy"
Cohesion: 0.10
Nodes (11): Request, TaxonomyController, Booking, Destination, FormEntry, Taxonomy, BelongsToMany, Term (+3 more)

### Community 11 - "Block"
Cohesion: 0.15
Nodes (6): Block, Contact, HeroBanner, LatestBlog, PackageHighlights, TeamCards

### Community 12 - "command"
Cohesion: 0.10
Nodes (20): FIRECRAWL_API_KEY, command, env, type, command, enabled, type, mcp (+12 more)

### Community 14 - "tiptap.js"
Cohesion: 0.46
Nodes (6): mountTipTap(), ResizableImage, setupImageToolbar(), setupResizeHandle(), setupToolbarOverflow(), updateActiveButtons()

### Community 15 - "Field.php"
Cohesion: 0.15
Nodes (4): AboutIntro, DestinationsGrid, PackageAbout, PageBanner

### Community 16 - "AppServiceProvider.php"
Cohesion: 0.12
Nodes (7): FormController, Form, AppServiceProvider, FormFieldTypes, Controller, Request, ServiceProvider

### Community 20 - "DestinationsGrid.php"
Cohesion: 0.10
Nodes (20): Admin CRUD, `admin.layouts.create`, `admin.layouts.edit`, `admin.layouts.index`, Admin Nav, Admin Views, Data Model, Database Seeder (+12 more)

### Community 22 - "HeroBanner.php"
Cohesion: 0.11
Nodes (17): Architecture Testing, Assertions, Basic Test Structure, Basic Usage, Browser Test Example, Common Pitfalls, Creating Tests, Datasets (+9 more)

### Community 26 - "PageBanner.php"
Cohesion: 0.11
Nodes (17): Architecture Testing, Assertions, Basic Test Structure, Basic Usage, Browser Test Example, Common Pitfalls, Creating Tests, Datasets (+9 more)

### Community 27 - "Contact.php"
Cohesion: 0.12
Nodes (15): 1. PHP Block Class, 2. Blade View, Architecture Overview, Code of Conduct, Contributing, Creating a Block (Two Files), Creating Content Blocks, Editor Integration Attributes (+7 more)

### Community 121 - "Seeder"
Cohesion: 0.17
Nodes (6): AdminUserController, RedirectResponse, Request, View, Admin, Authenticatable

### Community 122 - "opencode.json"
Cohesion: 0.50
Nodes (3): plugin, $schema, .opencode/plugins/graphify.js

### Community 136 - "RedirectResponse"
Cohesion: 0.25
Nodes (4): Request, PreviewController, Controller, BlockPreview

### Community 137 - "View"
Cohesion: 0.20
Nodes (5): Request, PostController, BlogController, BelongsToMany, Post

### Community 144 - "Lara-CMS — Full Rebuild Specification"
Cohesion: 0.13
Nodes (14): 11. Field editor widgets (Alpine), 12. Global sections (site-wide navbar/footer), 14. Settings, SEO, Taxonomies, Assets, Users, 15. Theme system, 17. Build order / milestones, 1. Core concept & mental model, 2. Tech stack & project setup, 3. Architecture & directory layout (+6 more)

### Community 145 - ".view"
Cohesion: 0.13
Nodes (7): AdminFactory, static, FormFactory, LayoutFactory, PackageFactory, PostFactory, Factory

### Community 146 - "Package"
Cohesion: 0.27
Nodes (3): PackageController, Request, Package

### Community 147 - "Tailwind CSS Development"
Cohesion: 0.17
Nodes (11): Basic Usage, Common Patterns, Common Pitfalls, Dark Mode, Documentation, Flexbox Layout, Grid Layout, Spacing (+3 more)

### Community 148 - "Layout"
Cohesion: 0.30
Nodes (3): LayoutController, Request, Layout

### Community 149 - "Security Best Practices"
Cohesion: 0.17
Nodes (11): Audit Dependencies, Authorize Every Action, CSRF Protection, Encrypt Sensitive Database Fields, Escape Output to Prevent XSS, Keep Secrets Out of Code, Mass Assignment Protection, Prevent SQL Injection (+3 more)

### Community 150 - "Tailwind CSS Development"
Cohesion: 0.17
Nodes (11): Basic Usage, Common Patterns, Common Pitfalls, Dark Mode, Documentation, Flexbox Layout, Grid Layout, Spacing (+3 more)

### Community 151 - "Architecture Best Practices"
Cohesion: 0.17
Nodes (11): Architecture Best Practices, Code to Interfaces, Convention Over Configuration, Default Sort by Descending, Single-Purpose Action Classes, Use Atomic Locks for Race Conditions, Use `Concurrency::run()` for Parallel Execution, Use `Context` for Request-Scoped Data (+3 more)

### Community 152 - "Queue & Job Best Practices"
Cohesion: 0.18
Nodes (10): Always Implement `failed()`, Batch Related Jobs, Implement `ShouldBeUnique`, Queue & Job Best Practices, Rate Limit External API Calls in Jobs, `retryUntil()` Needs `$tries = 0`, Set `retry_after` Greater Than `timeout`, Use Exponential Backoff (+2 more)

### Community 153 - "Security Best Practices"
Cohesion: 0.17
Nodes (11): Audit Dependencies, Authorize Every Action, CSRF Protection, Encrypt Sensitive Database Fields, Escape Output to Prevent XSS, Keep Secrets Out of Code, Mass Assignment Protection, Prevent SQL Injection (+3 more)

### Community 154 - "Architecture Best Practices"
Cohesion: 0.18
Nodes (11): Architecture Best Practices, Code to Interfaces, Convention Over Configuration, Default Sort by Descending, Single-Purpose Action Classes, Use Atomic Locks for Race Conditions, Use `Concurrency::run()` for Parallel Execution, Use `Context` for Request-Scoped Data (+3 more)

### Community 155 - "4. Data model & migrations"
Cohesion: 0.18
Nodes (11): 4. Data model & migrations, `assets`, `bookings`, Model casts, `packages`, `PageMeta` shape (the `pages.meta` JSON), `pages`, `posts` (blog) (+3 more)

### Community 156 - "Advanced Query Patterns"
Cohesion: 0.20
Nodes (9): Advanced Query Patterns, Create Dynamic Relationships via Subquery FK, Prefer `whereIn` + Subquery Over `whereHas`, Sometimes Two Simple Queries Beat One Complex Query, Use `addSelect()` Subqueries for Single Values from Has-Many, Use Compound Indexes Matching `orderBy` Column Order, Use Conditional Aggregates Instead of Multiple Count Queries, Use Correlated Subqueries for Has-Many Ordering (+1 more)

### Community 157 - "Database Performance Best Practices"
Cohesion: 0.20
Nodes (9): Add Database Indexes, Always Eager Load Relationships, Chunk Large Datasets, Database Performance Best Practices, No Queries in Blade Templates, Prevent Lazy Loading in Development, Select Only Needed Columns, Use `cursor()` for Memory-Efficient Iteration (+1 more)

### Community 158 - "Events & Notifications Best Practices"
Cohesion: 0.20
Nodes (9): Always Queue Notifications, Events & Notifications Best Practices, Implement `HasLocalePreference` on Notifiable Models, Rely on Event Discovery, Route Notification Channels to Dedicated Queues, Run `event:cache` in Production Deploy, Use `afterCommit()` on Notifications in Transactions, Use On-Demand Notifications for Non-User Recipients (+1 more)

### Community 159 - "Advanced Query Patterns"
Cohesion: 0.20
Nodes (9): Advanced Query Patterns, Create Dynamic Relationships via Subquery FK, Prefer `whereIn` + Subquery Over `whereHas`, Sometimes Two Simple Queries Beat One Complex Query, Use `addSelect()` Subqueries for Single Values from Has-Many, Use Compound Indexes Matching `orderBy` Column Order, Use Conditional Aggregates Instead of Multiple Count Queries, Use Correlated Subqueries for Has-Many Ordering (+1 more)

### Community 160 - "Database Performance Best Practices"
Cohesion: 0.20
Nodes (9): Add Database Indexes, Always Eager Load Relationships, Chunk Large Datasets, Database Performance Best Practices, No Queries in Blade Templates, Prevent Lazy Loading in Development, Select Only Needed Columns, Use `cursor()` for Memory-Efficient Iteration (+1 more)

### Community 161 - "Events & Notifications Best Practices"
Cohesion: 0.20
Nodes (9): Always Queue Notifications, Events & Notifications Best Practices, Implement `HasLocalePreference` on Notifiable Models, Rely on Event Discovery, Route Notification Channels to Dedicated Queues, Run `event:cache` in Production Deploy, Use `afterCommit()` on Notifications in Transactions, Use On-Demand Notifications for Non-User Recipients (+1 more)

### Community 162 - "Queue & Job Best Practices"
Cohesion: 0.18
Nodes (10): Always Implement `failed()`, Batch Related Jobs, Implement `ShouldBeUnique`, Queue & Job Best Practices, Rate Limit External API Calls in Jobs, `retryUntil()` Needs `$tries = 0`, Set `retry_after` Greater Than `timeout`, Use Exponential Backoff (+2 more)

### Community 163 - "Docker MySQL + phpMyAdmin Setup"
Cohesion: 0.20
Nodes (9): Docker MySQL + phpMyAdmin Setup, File Changes, Laravel .env Changes, MySQL 8.0, Non-Goals, phpMyAdmin, Purpose, Services (+1 more)

### Community 164 - "Caching Best Practices"
Cohesion: 0.22
Nodes (8): Caching Best Practices, Configure Failover Cache Stores in Production, Use `Cache::add()` for Atomic Conditional Writes, Use `Cache::flexible()` for Stale-While-Revalidate, Use `Cache::memo()` to Avoid Redundant Hits Within a Request, Use `Cache::remember()` Instead of Manual Get/Put, Use Cache Tags to Invalidate Related Groups, Use `once()` for Per-Request Memoization

### Community 165 - "Eloquent Best Practices"
Cohesion: 0.22
Nodes (8): Apply Global Scopes Sparingly, Avoid Hardcoded Table Names in Queries, Cast Date Columns Properly, Define Attribute Casts, Eloquent Best Practices, Use Correct Relationship Types, Use Local Scopes for Reusable Queries, Use `whereBelongsTo()` for Relationship Queries

### Community 166 - "Migration Best Practices"
Cohesion: 0.22
Nodes (8): Add Indexes in the Migration, Generate Migrations with Artisan, Keep Migrations Focused, Migration Best Practices, Mirror Defaults in Model `$attributes`, Never Modify Deployed Migrations, Use `constrained()` for Foreign Keys, Write Reversible `down()` Methods by Default

### Community 167 - "Caching Best Practices"
Cohesion: 0.22
Nodes (8): Caching Best Practices, Configure Failover Cache Stores in Production, Use `Cache::add()` for Atomic Conditional Writes, Use `Cache::flexible()` for Stale-While-Revalidate, Use `Cache::memo()` to Avoid Redundant Hits Within a Request, Use `Cache::remember()` Instead of Manual Get/Put, Use Cache Tags to Invalidate Related Groups, Use `once()` for Per-Request Memoization

### Community 168 - "Eloquent Best Practices"
Cohesion: 0.22
Nodes (8): Apply Global Scopes Sparingly, Avoid Hardcoded Table Names in Queries, Cast Date Columns Properly, Define Attribute Casts, Eloquent Best Practices, Use Correct Relationship Types, Use Local Scopes for Reusable Queries, Use `whereBelongsTo()` for Relationship Queries

### Community 169 - "Migration Best Practices"
Cohesion: 0.22
Nodes (8): Add Indexes in the Migration, Generate Migrations with Artisan, Keep Migrations Focused, Migration Best Practices, Mirror Defaults in Model `$attributes`, Never Modify Deployed Migrations, Use `constrained()` for Foreign Keys, Write Reversible `down()` Methods by Default

### Community 170 - "Layouts Collection Implementation Plan"
Cohesion: 0.22
Nodes (8): Layouts Collection Implementation Plan, Task 1: Migration + Model, Task 2: Factory + Seeder, Task 3: Admin Routes + LayoutController, Task 4: Admin Views (index, create, edit), Task 5: Admin Nav Link, Task 6: Integration with Page/Post Create Forms, Task 7: Tests

### Community 171 - "SKILL.md"
Cohesion: 0.33
Nodes (5): Consistency First, Decision Rules, How to Apply, Laravel Best Practices, Rule Index

### Community 172 - "Blade & Views Best Practices"
Cohesion: 0.25
Nodes (7): Blade & Views Best Practices, Prefer Blade Components Over `@include`, Use `$attributes->merge()` in Component Templates, Use `@aware` for Deeply Nested Component Props, Use Blade Fragments for Partial Re-Renders (htmx/Turbo), Use `@pushOnce` for Per-Component Scripts, Use View Composers for Shared View Data

### Community 173 - "Error Handling Best Practices"
Cohesion: 0.25
Nodes (7): Add Context to Exception Classes, Enable `dontReportDuplicates()`, Error Handling Best Practices, Exception Reporting and Rendering, Force JSON Error Rendering for API Routes, Throttle High-Volume Exceptions, Use `ShouldntReport` for Exceptions That Should Never Log

### Community 174 - "Task Scheduling Best Practices"
Cohesion: 0.25
Nodes (7): Task Scheduling Best Practices, Use `environments()` to Restrict Tasks, Use `onOneServer()` on Multi-Server Deployments, Use `runInBackground()` for Concurrent Long Tasks, Use Schedule Groups for Shared Configuration, Use `takeUntilTimeout()` for Time-Bounded Processing, Use `withoutOverlapping()` on Variable-Duration Tasks

### Community 175 - "Testing Best Practices"
Cohesion: 0.25
Nodes (7): Call `Event::fake()` After Factory Setup, Testing Best Practices, Use `Exceptions::fake()` to Assert Exception Reporting, Use Factory States and Sequences, Use `LazilyRefreshDatabase` Over `RefreshDatabase`, Use Model Assertions Over Raw Database Assertions, Use `recycle()` to Share Relationship Instances Across Factories

### Community 176 - "SKILL.md"
Cohesion: 0.29
Nodes (5): Consistency First, Decision Rules, How to Apply, Laravel Best Practices, Rule Index

### Community 177 - "Blade & Views Best Practices"
Cohesion: 0.25
Nodes (7): Blade & Views Best Practices, Prefer Blade Components Over `@include`, Use `$attributes->merge()` in Component Templates, Use `@aware` for Deeply Nested Component Props, Use Blade Fragments for Partial Re-Renders (htmx/Turbo), Use `@pushOnce` for Per-Component Scripts, Use View Composers for Shared View Data

### Community 178 - "Error Handling Best Practices"
Cohesion: 0.25
Nodes (7): Add Context to Exception Classes, Enable `dontReportDuplicates()`, Error Handling Best Practices, Exception Reporting and Rendering, Force JSON Error Rendering for API Routes, Throttle High-Volume Exceptions, Use `ShouldntReport` for Exceptions That Should Never Log

### Community 179 - "Task Scheduling Best Practices"
Cohesion: 0.25
Nodes (7): Task Scheduling Best Practices, Use `environments()` to Restrict Tasks, Use `onOneServer()` on Multi-Server Deployments, Use `runInBackground()` for Concurrent Long Tasks, Use Schedule Groups for Shared Configuration, Use `takeUntilTimeout()` for Time-Bounded Processing, Use `withoutOverlapping()` on Variable-Duration Tasks

### Community 180 - "Testing Best Practices"
Cohesion: 0.25
Nodes (7): Call `Event::fake()` After Factory Setup, Testing Best Practices, Use `Exceptions::fake()` to Assert Exception Reporting, Use Factory States and Sequences, Use `LazilyRefreshDatabase` Over `RefreshDatabase`, Use Model Assertions Over Raw Database Assertions, Use `recycle()` to Share Relationship Instances Across Factories

### Community 181 - "10. The Visual Editor (the crown jewel)"
Cohesion: 0.25
Nodes (8): 10.1 Layout, 10.2 State (Alpine store), 10.3 The auto form (recursive, schema-driven), 10.4 Path helpers (JS), 10.5 Live preview + click-to-edit, 10.6 Add-section picker, 10.7 Save, 10. The Visual Editor (the crown jewel)

### Community 182 - "Collection Best Practices"
Cohesion: 0.29
Nodes (6): Choose `cursor()` vs. `lazy()` Correctly, Collection Best Practices, Use `#[CollectedBy]` for Custom Collection Classes, Use Higher-Order Messages for Simple Operations, Use `lazyById()` When Updating Records While Iterating, Use `toQuery()` for Bulk Operations on Collections

### Community 183 - "HTTP Client Best Practices"
Cohesion: 0.29
Nodes (6): Always Set Explicit Timeouts, Fake HTTP Calls in Tests, Handle Errors Explicitly, HTTP Client Best Practices, Use Request Pooling for Concurrent Requests, Use Retry with Backoff for External APIs

### Community 184 - "Mail Best Practices"
Cohesion: 0.29
Nodes (6): Implement `ShouldQueue` on the Mailable Class, Mail Best Practices, Separate Content Tests from Sending Tests, Use `afterCommit()` on Mailables Inside Transactions, Use `assertQueued()` Not `assertSent()` for Queued Mailables, Use Markdown Mailables for Transactional Emails

### Community 185 - "Routing & Controllers Best Practices"
Cohesion: 0.29
Nodes (6): Keep Controllers Thin, Routing & Controllers Best Practices, Type-Hint Form Requests, Use Implicit Route Model Binding, Use Resource Controllers, Use Scoped Bindings for Nested Resources

### Community 186 - "Conventions & Style"
Cohesion: 0.29
Nodes (6): Conventions & Style, Follow Laravel Naming Conventions, No Inline JS/CSS in Blade, No Unnecessary Comments, Prefer Shorter Readable Syntax, Use Laravel String & Array Helpers

### Community 187 - "Validation & Forms Best Practices"
Cohesion: 0.29
Nodes (6): Always Use `validated()`, Array vs. String Notation for Rules, Use Form Request Classes, Use `Rule::when()` for Conditional Validation, Use the `after()` Method for Custom Validation, Validation & Forms Best Practices

### Community 188 - "Collection Best Practices"
Cohesion: 0.29
Nodes (6): Choose `cursor()` vs. `lazy()` Correctly, Collection Best Practices, Use `#[CollectedBy]` for Custom Collection Classes, Use Higher-Order Messages for Simple Operations, Use `lazyById()` When Updating Records While Iterating, Use `toQuery()` for Bulk Operations on Collections

### Community 189 - "HTTP Client Best Practices"
Cohesion: 0.29
Nodes (6): Always Set Explicit Timeouts, Fake HTTP Calls in Tests, Handle Errors Explicitly, HTTP Client Best Practices, Use Request Pooling for Concurrent Requests, Use Retry with Backoff for External APIs

### Community 190 - "Mail Best Practices"
Cohesion: 0.29
Nodes (6): Implement `ShouldQueue` on the Mailable Class, Mail Best Practices, Separate Content Tests from Sending Tests, Use `afterCommit()` on Mailables Inside Transactions, Use `assertQueued()` Not `assertSent()` for Queued Mailables, Use Markdown Mailables for Transactional Emails

### Community 191 - "Routing & Controllers Best Practices"
Cohesion: 0.29
Nodes (6): Keep Controllers Thin, Routing & Controllers Best Practices, Type-Hint Form Requests, Use Implicit Route Model Binding, Use Resource Controllers, Use Scoped Bindings for Nested Resources

### Community 192 - "Conventions & Style"
Cohesion: 0.29
Nodes (6): Conventions & Style, Follow Laravel Naming Conventions, No Inline JS/CSS in Blade, No Unnecessary Comments, Prefer Shorter Readable Syntax, Use Laravel String & Array Helpers

### Community 193 - "Validation & Forms Best Practices"
Cohesion: 0.29
Nodes (6): Always Use `validated()`, Array vs. String Notation for Rules, Use Form Request Classes, Use `Rule::when()` for Conditional Validation, Use the `after()` Method for Custom Validation, Validation & Forms Best Practices

### Community 194 - "Appendix A — full block catalog"
Cohesion: 0.29
Nodes (7): Appendix A — full block catalog, Blog collection blocks, Global blocks (`global: true`, shared site-wide), Home collection blocks, Package detail blocks (stored in `packages.blocks`), Packages collection blocks, Reference implementation notes

### Community 195 - "Configuration Best Practices"
Cohesion: 0.33
Nodes (5): Configuration Best Practices, `env()` Only in Config Files, Use `App::environment()` for Environment Checks, Use Constants and Language Files, Use Encrypted Env or External Secrets

### Community 196 - "Configuration Best Practices"
Cohesion: 0.33
Nodes (5): Configuration Best Practices, `env()` Only in Config Files, Use `App::environment()` for Environment Checks, Use Constants and Language Files, Use Encrypted Env or External Secrets

### Community 197 - "13. Other collections: Blog, Packages, Bookings"
Cohesion: 0.40
Nodes (5): 13.1 Content-type config, 13.2 Package detail blocks, 13.3 Admin screens, 13.4 Booking + payment flow (optional), 13. Other collections: Blog, Packages, Bookings

### Community 198 - "5. The block system (the engine)"
Cohesion: 0.40
Nodes (5): 5.1 A field definition (`FieldDef`), 5.2 A block definition (`Block`), 5.3 The registry, 5.4 Default data from schema, 5. The block system (the engine)

### Community 199 - "7. Public website rendering"
Cohesion: 0.40
Nodes (5): 7.1 Routes (`routes/web.php`), 7.2 The renderer, 7.3 A block view, 7.4 Public layout, 7. Public website rendering

### Community 200 - "16. Routes reference"
Cohesion: 0.50
Nodes (4): 16. Routes reference, Admin (`admin.php`, prefix `admin`, `auth`), Public (`web.php`), Reference API payloads (JSON shapes)

### Community 201 - "8. Admin panel — shell, nav, layout"
Cohesion: 0.50
Nodes (4): 8.1 Layout (`admin/layout.blade.php`), 8.2 Sidebar nav groups (`nav-client`), 8.3 Dashboard (`/admin`), 8. Admin panel — shell, nav, layout

### Community 214 - "9. Pages CRUD"
Cohesion: 0.67
Nodes (3): 9.1 List (`/admin/pages`), 9.2 Create / edit page settings (Page Entry Form), 9. Pages CRUD

### Community 222 - "SeoController.php"
Cohesion: 0.21
Nodes (5): AdminSeeder, DatabaseSeeder, LayoutSeeder, UserSeeder, Seeder

### Community 223 - "Model"
Cohesion: 0.35
Nodes (4): RedirectResponse, Request, View, UserController

### Community 224 - "Term.php"
Cohesion: 0.52
Nodes (3): User, HasFactory, Notifiable

## Knowledge Gaps
- **557 isolated node(s):** `php`, `$schema`, `.opencode/plugins/graphify.js`, `$schema`, `name` (+552 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **46 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Block` connect `Block` to `Page`, `BlockRegistry`, `Field.php`, `BlogPostSlot.php`, `ClientTestimonials.php`, `FeatureImageCards.php`, `LatestBlog.php`, `PackageList.php`, `PackagePostSlot.php`, `TeamCards.php`, `TravelDeals.php`, `WhyChooseUs.php`, `SiteFooter.php`, `SiteNavbar.php`, `SiteTopBar.php`, `BlogList.php`, `BlogSection.php`, `PackageFaq.php`, `PackageFeatures.php`, `PackageGalleryHero.php`, `PackageHero.php`, `PackageInfo.php`, `PackageLocations.php`, `PackageMap.php`, `SimpleText.php`, `TravelDeals.php`, `ProfileBento.php`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Why does `Controller` connect `RedirectResponse` to `Page`, `Controller`, `Model`, `LoginController.php`, `View`, `Taxonomy`, `Package`, `Layout`, `Seeder`, `Model`?**
  _High betweenness centrality (0.013) - this node is a cross-community bridge._
- **Why does `Admin` connect `Seeder` to `Term.php`, `Model`, `LoginController.php`, `Taxonomy`, `.view`, `SeoController.php`?**
  _High betweenness centrality (0.012) - this node is a cross-community bridge._
- **Are the 34 inferred relationships involving `Field` (e.g. with `.resolvedFields()` and `.fields()`) actually correct?**
  _`Field` has 34 INFERRED edges - model-reasoned connections that need verification._
- **What connects `php`, `$schema`, `.opencode/plugins/graphify.js` to the rest of the system?**
  _557 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `dependencies` be split into smaller, more focused modules?**
  _Cohesion score 0.0425531914893617 - nodes in this community are weakly interconnected._
- **Should `Layout` be split into smaller, more focused modules?**
  _Cohesion score 0.07407407407407407 - nodes in this community are weakly interconnected._