# Graph Report - lara-cms  (2026-07-25)

## Corpus Check
- 604 files · ~448,586 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2219 nodes · 2865 edges · 381 communities (324 shown, 57 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 184 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `85d486b4`
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
- 2026_07_10_185347_create_bookings_table.php
- FormController
- 2026_07_10_185347_create_posts_table.php
- require-dev
- Collection.php
- FormEntry
- laravel-boost
- User
- artisan
- docker-entrypoint.sh
- UserController
- CommandSearch
- Widget
- CampaignController.php
- blog-list.blade.php
- contact.blade.php
- .sendCampaign
- PreviewController.php
- TrackPageViews.php
- CollectionEntryController.php
- config
- FormEntry
- pusher-js
- BlogList.php
- site-footer.blade.php
- @templatical/quality
- @tiptap/core
- PagesWidget
- SlaTableWidget
- StatWidget
- RedirectResponse
- View
- UpdatesListWidget
- index.blade.php
- WebsiteAnalyticsWidget
- Lara-CMS — Full Rebuild Specification
- WhyChooseUs.php
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
- Docker MySQL + phpMyAdmin Setup
- Caching Best Practices
- Eloquent Best Practices
- Migration Best Practices
- Layouts Collection Implementation Plan
- DashboardController.php
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
- SettingsController.php
- Form.php
- Systematic Debugging
- Persuasion Principles for Skill Design
- BlogList.php
- Appendix A — full block catalog
- Finishing a Development Branch
- LatestBlog.php
- 13. Other collections: Blog, Packages, Bookings
- 5. The block system (the engine)
- 7. Public website rendering
- 16. Routes reference
- 8. Admin panel — shell, nav, layout
- BlogList.php
- FormEntry
- PageBanner.php
- SimpleText.php
- WhyChooseUs.php
- SiteNavbar.php
- Using Git Worktrees
- Writing Skills
- Dispatching Parallel Agents
- ProfileBento.php
- Defense-in-Depth Validation
- 9. Pages CRUD
- Writing Plans
- [Analysis Title]
- Returns: "OK" or lists conflicts
- UserController
- AdminUserController
- Post.php
- Executing Plans
- Condition-Based Waiting
- @codemirror/lang-json
- Verification Before Completion
- Skill structure
- require-dev
- UpdatesListWidget
- helper.js
- Skill authoring best practices
- render-graphs.js
- BlockRegistry
- Brainstorming Ideas Into Designs
- setup
- WebsiteAnalyticsWidget
- stop-server.sh
- codex-tools.md
- sortablejs
- @tiptap/extension-link
- @tiptap/starter-kit
- Gemini CLI Tool Mapping
- Subscription
- config
- Seeder
- SKILL.md
- Skill Discovery Optimization (SDO)
- Bulletproofing Skills Against Rationalization
- Pressure Test 1: Emergency Production Fix
- Pressure Test 2: Sunk Cost + Exhaustion
- Pressure Test 3: Authority + Social Pressure
- anthropic-best-practices.md
- Anti-Patterns
- Testing All Skill Types
- RED-GREEN-REFACTOR for Skills
- SettingsController.php
- psr-4
- require
- UserFactory
- Pi Tool Mapping
- Evaluation and iteration
- Checklist for effective Skills
- Core principles
- File Organization
- Skill Types
- post-create-project-cmd
- start-server.sh
- Antigravity CLI (`agy`) Tool Mapping
- extra
- spec-document-reviewer-prompt.md
- review-package
- sdd-workspace
- task-brief
- find-polluter.sh
- test-academic.md
- plan-document-reviewer-prompt.md

## God Nodes (most connected - your core abstractions)
1. `Controller` - 67 edges
2. `Block` - 54 edges
3. `Admin` - 38 edges
4. `Field` - 37 edges
5. `Collection` - 35 edges
6. `Template` - 32 edges
7. `Stats` - 29 edges
8. `CollectionEntry` - 27 edges
9. `Setting` - 24 edges
10. `Campaign` - 23 edges

## Surprising Connections (you probably didn't know these)
- `BlogDetails` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/BlogDetails.php → app/Blocks/Block.php
- `BlogList` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/BlogList.php → app/Blocks/Block.php
- `BlogPostSlot` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/BlogPostSlot.php → app/Blocks/Block.php
- `ClientTestimonials` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/ClientTestimonials.php → app/Blocks/Block.php
- `DestinationsGrid` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/DestinationsGrid.php → app/Blocks/Block.php

## Import Cycles
- None detected.

## Communities (381 total, 57 thin omitted)

### Community 0 - "dependencies"
Cohesion: 0.10
Nodes (21): autoprefixer, concurrently, @fortawesome/fontawesome-free, laravel-vite-plugin, devDependencies, autoprefixer, concurrently, @fortawesome/fontawesome-free (+13 more)

### Community 1 - "Layout"
Cohesion: 0.07
Nodes (26): APIs & Eloquent Resources, Application Structure & Architecture, Artisan, Conventions, Deployment, Do Things the Laravel Way, Documentation Files, Foundational Context (+18 more)

### Community 2 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 3 - "Page"
Cohesion: 0.16
Nodes (4): ContactController, Request, Group, HasMany

### Community 4 - "Controller"
Cohesion: 0.11
Nodes (19): chrome-devtools-mcp, codemirror, @codemirror/lang-json, dependencies, chrome-devtools-mcp, codemirror, @codemirror/lang-json, @templatical/editor (+11 more)

### Community 5 - "Post"
Cohesion: 0.07
Nodes (27): APIs & Eloquent Resources, Application Structure & Architecture, Artisan, Conventions, Deployment, Do Things the Laravel Way, Documentation Files, Foundational Context (+19 more)

### Community 6 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+7 more)

### Community 7 - "Model"
Cohesion: 0.21
Nodes (3): FormEntriesTableWidget, Form, static

### Community 8 - "LoginController.php"
Cohesion: 0.18
Nodes (10): ProcessEmailFileJob, SendEmailJob, SendSingleEmailJob, WeeklyReportMail, Dispatchable, InteractsWithQueue, Mailable, Queueable (+2 more)

### Community 9 - "Taxonomy"
Cohesion: 0.13
Nodes (5): EmailTemplateController, Request, Request, TemplateController, Template

### Community 11 - "Block"
Cohesion: 0.15
Nodes (5): Block, AboutIntro, BlogSection, Contact, TravelDetails

### Community 12 - "command"
Cohesion: 0.08
Nodes (25): FIRECRAWL_API_KEY, command, env, type, command, enabled, type, command (+17 more)

### Community 13 - "BlockRegistry"
Cohesion: 0.11
Nodes (10): RedirectResponse, View, ProfileController, LoginController, RedirectResponse, Request, View, LoginRequest (+2 more)

### Community 14 - "tiptap.js"
Cohesion: 0.46
Nodes (6): mountTipTap(), ResizableImage, setupImageToolbar(), setupResizeHandle(), setupToolbarOverflow(), updateActiveButtons()

### Community 18 - "BlogPostSlot.php"
Cohesion: 0.10
Nodes (19): 10. Features Loaded Globally (Should Be Per-Page), 1. Render Delay Eats 89.5% of LCP (Dashboard), 2. 46+ CSS/JS Files Are Render-Blocking, 3. Zero Cache on Local Assets, 4. Slow PHP Backend Endpoints, 5. Page Double-Load Bug, 6. Third-Party Bloat, 7. No Preconnect Hints (+11 more)

### Community 20 - "DestinationsGrid.php"
Cohesion: 0.10
Nodes (20): Admin CRUD, `admin.layouts.create`, `admin.layouts.edit`, `admin.layouts.index`, Admin Nav, Admin Views, Data Model, Database Seeder (+12 more)

### Community 22 - "HeroBanner.php"
Cohesion: 0.11
Nodes (17): Architecture Testing, Assertions, Basic Test Structure, Basic Usage, Browser Test Example, Common Pitfalls, Creating Tests, Datasets (+9 more)

### Community 24 - "PackageList.php"
Cohesion: 0.19
Nodes (6): Request, ReportController, Request, TrackerController, BelongsTo, Stats

### Community 25 - "PackagePostSlot.php"
Cohesion: 0.25
Nodes (3): AssetsController, Request, Asset

### Community 26 - "PageBanner.php"
Cohesion: 0.12
Nodes (16): Admin UI, `App\Models\Subscription`, `App\Models\SubscriptionPlan` (add), Campaign Limit Check, Email Limit Check, Files Changed, Flow, Future Payment Integration (+8 more)

### Community 27 - "Contact.php"
Cohesion: 0.12
Nodes (15): 1. PHP Block Class, 2. Blade View, Architecture Overview, Code of Conduct, Contributing, Creating a Block (Two Files), Creating Content Blocks, Editor Integration Attributes (+7 more)

### Community 28 - "TeamCards.php"
Cohesion: 0.15
Nodes (3): Admin, BelongsToMany, Authenticatable

### Community 29 - "TravelDeals.php"
Cohesion: 0.28
Nodes (3): LayoutController, Request, Layout

### Community 31 - "SiteFooter.php"
Cohesion: 0.09
Nodes (11): Controller, AccountController, Request, FormController, Request, Request, SearchController, SupportController (+3 more)

### Community 32 - "SiteNavbar.php"
Cohesion: 0.13
Nodes (8): AdminFactory, static, CollectionFactory, FormEntryFactory, FormFactory, LayoutFactory, PostFactory, Factory

### Community 38 - "2026_07_10_185347_create_bookings_table.php"
Cohesion: 0.22
Nodes (8): Global Constraints, Subscription Management Implementation Plan, Task 1: Create Subscriptions Migration + Model, Task 2: Update RegisterController, Task 3: Update CampaignController Limits, Task 4: Update ContactController + GroupController Limits, Task 5: Admin User Edit — Subscription Management, Task 6: Verify

### Community 39 - "FormController"
Cohesion: 0.06
Nodes (26): Code Reviewer Prompt Template, Example Output, Common Rationalizations, Example, How to Request, Red Flags, Requesting Code Review, When to Request Review (+18 more)

### Community 40 - "2026_07_10_185347_create_posts_table.php"
Cohesion: 0.30
Nodes (3): CampaignController, Request, Campaign

### Community 43 - "require-dev"
Cohesion: 0.24
Nodes (4): CollectionController, Request, Collection, HasMany

### Community 45 - "Collection.php"
Cohesion: 0.12
Nodes (4): BelongsTo, BelongsToMany, Term, BlogSidebarData

### Community 54 - "User"
Cohesion: 0.17
Nodes (4): Request, SeoController, Page, Seo

### Community 70 - "UserController"
Cohesion: 0.33
Nodes (5): Configuration Best Practices, `env()` Only in Config Files, Use `App::environment()` for Environment Checks, Use Constants and Language Files, Use Encrypted Env or External Secrets

### Community 86 - "CommandSearch"
Cohesion: 0.06
Nodes (29): Common Rationalizations, Debugging Integration, Example: Bug Fix, Final Rule, Good Tests, GREEN - Minimal Code, Overview, Red Flags - STOP and Start Over (+21 more)

### Community 87 - "Widget"
Cohesion: 0.18
Nodes (4): Request, SubscriptionPlanController, SubscriptionPlan, Attribute

### Community 88 - "CampaignController.php"
Cohesion: 0.08
Nodes (11): CommandSearchController, Request, Request, PreviewController, Request, TaxonomyController, Taxonomy, WidgetLayout (+3 more)

### Community 95 - "blog-list.blade.php"
Cohesion: 0.18
Nodes (5): UpdateController, Request, SettingController, Sender, Setting

### Community 98 - "contact.blade.php"
Cohesion: 0.14
Nodes (11): Always Set Explicit Timeouts, Fake HTTP Calls in Tests, Handle Errors Explicitly, HTTP Client Best Practices, Use Request Pooling for Concurrent Requests, Use Retry with Backoff for External APIs, Consistency First, Decision Rules (+3 more)

### Community 101 - "PreviewController.php"
Cohesion: 0.07
Nodes (29): 1. Explicit Negation in Rules, 2. Entry in Rationalization Table, 3. Red Flag Entry, 4. Update description, Common Mistakes (Same as TDD), Example: TDD Skill Bulletproofing, GREEN Phase: Write Minimal Skill (Make It Pass), Initial Test (Failed) (+21 more)

### Community 102 - "TrackPageViews.php"
Cohesion: 0.10
Nodes (19): Browser Events Format, Cards (visual designs), Cleaning Up, CSS Classes Available, Design Tips, File Naming, How It Works, Mock elements (wireframe building blocks) (+11 more)

### Community 103 - "CollectionEntryController.php"
Cohesion: 0.67
Nodes (3): alpinejs, alpinejs, alpinejs

### Community 104 - "config"
Cohesion: 0.52
Nodes (4): Request, TrackPageViews, Closure, Response

### Community 107 - "BlogList.php"
Cohesion: 0.10
Nodes (19): Bulletproofing Elements, Creation Log: Systematic Debugging Skill, Enhancement 1: TDD Reference, Extraction Decisions, Final Outcome, Initial Version, Iterations, Key Insight (+11 more)

### Community 108 - "site-footer.blade.php"
Cohesion: 0.20
Nodes (5): DashboardController, Request, BelongsTo, HasMany, HasOne

### Community 113 - "PagesWidget"
Cohesion: 0.14
Nodes (4): PageController, CollectionEntry, BelongsTo, PagesWidget

### Community 136 - "RedirectResponse"
Cohesion: 0.11
Nodes (5): AppServiceProvider, PluginServiceProvider, PluginLoader, WidgetRegistry, ServiceProvider

### Community 137 - "View"
Cohesion: 0.19
Nodes (4): FormController, Form, Request, FormFieldTypes

### Community 144 - "Lara-CMS — Full Rebuild Specification"
Cohesion: 0.13
Nodes (14): 11. Field editor widgets (Alpine), 12. Global sections (site-wide navbar/footer), 14. Settings, SEO, Taxonomies, Assets, Users, 15. Theme system, 17. Build order / milestones, 1. Core concept & mental model, 2. Tech stack & project setup, 3. Architecture & directory layout (+6 more)

### Community 146 - "Package"
Cohesion: 0.07
Nodes (25): 1. System Architecture Overview, 2. Step 1: Create the PHP Block Class, 3. Step 2: Create the Blade Template View, 4. Prompt Template for AI / Vision Models (Screenshot to Block Converter), 5. Verification Checklist, Available `Field::` Helpers, Class Rules:, Lara CMS — Complete Block Creation Guide & AI Blueprint (+17 more)

### Community 147 - "Tailwind CSS Development"
Cohesion: 0.17
Nodes (11): Basic Usage, Common Patterns, Common Pitfalls, Dark Mode, Documentation, Flexbox Layout, Grid Layout, Spacing (+3 more)

### Community 148 - "Layout"
Cohesion: 0.29
Nodes (6): private, $schema, scripts, build, dev, type

### Community 151 - "Architecture Best Practices"
Cohesion: 0.17
Nodes (11): Architecture Best Practices, Code to Interfaces, Convention Over Configuration, Default Sort by Descending, Single-Purpose Action Classes, Use Atomic Locks for Race Conditions, Use `Concurrency::run()` for Parallel Execution, Use `Context` for Request-Scoped Data (+3 more)

### Community 152 - "Queue & Job Best Practices"
Cohesion: 0.18
Nodes (10): Always Implement `failed()`, Batch Related Jobs, Implement `ShouldBeUnique`, Queue & Job Best Practices, Rate Limit External API Calls in Jobs, `retryUntil()` Needs `$tries = 0`, Set `retry_after` Greater Than `timeout`, Use Exponential Backoff (+2 more)

### Community 153 - "Security Best Practices"
Cohesion: 0.18
Nodes (11): Audit Dependencies, Authorize Every Action, CSRF Protection, Encrypt Sensitive Database Fields, Escape Output to Prevent XSS, Keep Secrets Out of Code, Mass Assignment Protection, Prevent SQL Injection (+3 more)

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

### Community 170 - "Layouts Collection Implementation Plan"
Cohesion: 0.22
Nodes (8): Layouts Collection Implementation Plan, Task 1: Migration + Model, Task 2: Factory + Seeder, Task 3: Admin Routes + LayoutController, Task 4: Admin Views (index, create, edit), Task 5: Admin Nav Link, Task 6: Integration with Page/Post Create Forms, Task 7: Tests

### Community 171 - "DashboardController.php"
Cohesion: 0.12
Nodes (16): Acknowledging Correct Feedback, Code Review Reception, Common Mistakes, Forbidden Responses, From External Reviewers, From your human partner, GitHub Thread Replies, Gracefully Correcting Your Pushback (+8 more)

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

### Community 181 - "10. The Visual Editor (the crown jewel)"
Cohesion: 0.25
Nodes (8): 10.1 Layout, 10.2 State (Alpine store), 10.3 The auto form (recursive, schema-driven), 10.4 Path helpers (JS), 10.5 Live preview + click-to-edit, 10.6 Add-section picker, 10.7 Save, 10. The Visual Editor (the crown jewel)

### Community 182 - "Collection Best Practices"
Cohesion: 0.29
Nodes (6): Choose `cursor()` vs. `lazy()` Correctly, Collection Best Practices, Use `#[CollectedBy]` for Custom Collection Classes, Use Higher-Order Messages for Simple Operations, Use `lazyById()` When Updating Records While Iterating, Use `toQuery()` for Bulk Operations on Collections

### Community 183 - "HTTP Client Best Practices"
Cohesion: 0.12
Nodes (16): Documentation Variants to Test, Expected Results, Next Steps, NULL (Baseline - no skills doc), Scenario 1: Time Pressure + Confidence, Scenario 2: Sunk Cost + Works Already, Scenario 3: Authority + Speed Bias, Scenario 4: Familiarity + Efficiency (+8 more)

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

### Community 189 - "SettingsController.php"
Cohesion: 0.12
Nodes (15): 1. Observe the Symptom, 2. Find Immediate Cause, 3. Ask: What Called This?, 4. Keep Tracing Up, 5. Find Original Trigger, Adding Stack Traces, Finding Which Test Causes Pollution, Key Principle (+7 more)

### Community 191 - "Systematic Debugging"
Cohesion: 0.12
Nodes (15): Common Rationalizations, Overview, Phase 1: Root Cause Investigation, Phase 2: Pattern Analysis, Phase 3: Hypothesis and Testing, Phase 4: Implementation, Quick Reference, Red Flags - STOP and Follow Process (+7 more)

### Community 192 - "Persuasion Principles for Skill Design"
Cohesion: 0.12
Nodes (15): 1. Authority, 2. Commitment, 3. Scarcity, 4. Social Proof, 5. Unity, 6. Reciprocity, 7. Liking, Ethical Use (+7 more)

### Community 194 - "Appendix A — full block catalog"
Cohesion: 0.29
Nodes (7): Appendix A — full block catalog, Blog collection blocks, Global blocks (`global: true`, shared site-wide), Home collection blocks, Package detail blocks (stored in `packages.blocks`), Packages collection blocks, Reference implementation notes

### Community 195 - "Finishing a Development Branch"
Cohesion: 0.13
Nodes (14): Common Rationalizations, Finishing a Development Branch, If your human partner asks to discard the work, Option 1: Merge Locally, Option 2: Push and Create PR, Option 3: Keep As-Is, Overview, Quick Reference (+6 more)

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

### Community 208 - "Using Git Worktrees"
Cohesion: 0.13
Nodes (14): 1a. Native Worktree Tools (preferred), 1b. Git Worktree Fallback, Common Rationalizations, Create the Worktree, Directory Selection, Overview, Quick Reference, Report (+6 more)

### Community 209 - "Writing Skills"
Cohesion: 0.13
Nodes (15): Code Examples, Common Rationalizations for Skipping Testing, Directory Structure, Discovery Workflow, Flowchart Usage, Match the Form to the Failure, Overview, Skill Creation Checklist (TDD Adapted) (+7 more)

### Community 210 - "Dispatching Parallel Agents"
Cohesion: 0.14
Nodes (13): 1. Identify Independent Domains, 2. Create Focused Agent Tasks, 3. Dispatch in Parallel, 4. Review and Integrate, Agent Prompt Structure, Common Mistakes, Dispatching Parallel Agents, Overview (+5 more)

### Community 212 - "Defense-in-Depth Validation"
Cohesion: 0.17
Nodes (11): Applying the Pattern, Defense-in-Depth Validation, Example from Session, Key Insight, Layer 1: Entry Point Validation, Layer 2: Business Logic Validation, Layer 3: Environment Guards, Layer 4: Debug Instrumentation (+3 more)

### Community 214 - "9. Pages CRUD"
Cohesion: 0.67
Nodes (3): 9.1 List (`/admin/pages`), 9.2 Create / edit page settings (Page Entry Form), 9. Pages CRUD

### Community 221 - "Writing Plans"
Cohesion: 0.17
Nodes (11): Bite-Sized Task Granularity, Execution Handoff, File Structure, No Placeholders, Overview, Plan Document Header, Scope Check, Self-Review (+3 more)

### Community 222 - "[Analysis Title]"
Cohesion: 0.17
Nodes (12): Advanced: Skills with executable code, [Analysis Title], Anti-patterns to avoid, Avoid offering too many options, Avoid Windows-style paths, Conditional workflow pattern, Examples pattern, Executive summary (+4 more)

### Community 223 - "Returns: "OK" or lists conflicts"
Cohesion: 0.18
Nodes (11): Avoid assuming tools are installed, Create verifiable intermediate outputs, MCP tool references, Next steps, Package dependencies, Returns: "OK" or lists conflicts, Runtime environment, Technical notes (+3 more)

### Community 236 - "AdminUserController"
Cohesion: 0.33
Nodes (4): AdminUserController, RedirectResponse, Request, View

### Community 238 - "Post.php"
Cohesion: 0.14
Nodes (14): Booking, Destination, Form, HasMany, Contact, BelongsTo, DefaultSetting, Form (+6 more)

### Community 239 - "Executing Plans"
Cohesion: 0.20
Nodes (9): Executing Plans, Overview, Remember, Step 1: Load and Review Plan, Step 2: Execute Tasks, Step 3: Complete Development, The Process, When to Revisit Earlier Steps (+1 more)

### Community 253 - "Condition-Based Waiting"
Cohesion: 0.20
Nodes (9): Common Mistakes, Condition-Based Waiting, Core Pattern, Implementation, Overview, Quick Patterns, Real-World Impact, When Arbitrary Timeout IS Correct (+1 more)

### Community 254 - "@codemirror/lang-json"
Cohesion: 0.09
Nodes (14): ScheduleCampaign, SendReports, RedirectResponse, Request, View, UserController, RedirectResponse, Request (+6 more)

### Community 255 - "Verification Before Completion"
Cohesion: 0.20
Nodes (9): Common Failures, Key Patterns, Overview, Rationalization Prevention, Red Flags - STOP, The Gate Function, The Iron Law, Verification Before Completion (+1 more)

### Community 256 - "Skill structure"
Cohesion: 0.20
Nodes (10): Avoid deeply nested references, Naming conventions, Pattern 1: High-level guide with references, Pattern 2: Domain-specific organization, Pattern 3: Conditional details, Progressive disclosure patterns, Skill structure, Structure longer reference files with table of contents (+2 more)

### Community 257 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+2 more)

### Community 259 - "helper.js"
Cohesion: 0.42
Nodes (7): connect(), nextReconnectDelay(), reloadAfterRecovery(), sessionKey(), setStatus(), showTombstone(), websocketUrl()

### Community 262 - "Skill authoring best practices"
Cohesion: 0.22
Nodes (9): Avoid time-sensitive information, Common patterns, Content guidelines, Implement feedback loops, Skill authoring best practices, Template pattern, Use consistent terminology, Use workflows for complex tasks (+1 more)

### Community 263 - "render-graphs.js"
Cohesion: 0.33
Nodes (8): combineGraphs(), { execSync }, extractDotBlocks(), extractGraphBody(), fs, main(), path, renderToSvg()

### Community 265 - "Brainstorming Ideas Into Designs"
Cohesion: 0.25
Nodes (7): After the Design, Anti-Pattern: "This Is Too Simple To Need A Design", Brainstorming Ideas Into Designs, Checklist, Process Flow, The Process, Visual Companion

### Community 266 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 267 - "WebsiteAnalyticsWidget"
Cohesion: 0.40
Nodes (4): Creating a New Plugin, Example plugins, How it works, Plugins Directory

### Community 268 - "stop-server.sh"
Cohesion: 0.43
Nodes (4): command_has_server_id(), is_brainstorm_server(), mark_stopped(), stop-server.sh script

### Community 269 - "codex-tools.md"
Cohesion: 0.29
Nodes (3): Codex App Finishing, Environment Detection, Subagent dispatch requires multi-agent support

### Community 331 - "Gemini CLI Tool Mapping"
Cohesion: 0.29
Nodes (7): Additional Gemini CLI tools, Gemini CLI Tool Mapping, Instructions file, Parallel dispatch, Personal skills directory, Prompt filling, Subagent support

### Community 333 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 334 - "Seeder"
Cohesion: 0.38
Nodes (3): AdminSeeder, DatabaseSeeder, Seeder

### Community 335 - "SKILL.md"
Cohesion: 0.33
Nodes (5): Platform Adaptation, Red Flags, Skill Priority, The Rule, User Instructions

### Community 336 - "Skill Discovery Optimization (SDO)"
Cohesion: 0.33
Nodes (6): 1. Rich Description Field, 2. Keyword Coverage, 3. Descriptive Naming, 4. Token Efficiency (Critical), 5. Cross-Referencing Other Skills, Skill Discovery Optimization (SDO)

### Community 337 - "Bulletproofing Skills Against Rationalization"
Cohesion: 0.33
Nodes (6): Address "Spirit vs Letter" Arguments, Build Rationalization Table, Bulletproofing Skills Against Rationalization, Close Every Loophole Explicitly, Create Red Flags List, Update SDO for Violation Symptoms

### Community 344 - "Pressure Test 1: Emergency Production Fix"
Cohesion: 0.40
Nodes (4): Choose A, B, or C, Pressure Test 1: Emergency Production Fix, Scenario, Your Options

### Community 345 - "Pressure Test 2: Sunk Cost + Exhaustion"
Cohesion: 0.40
Nodes (4): Choose A, B, or C, Pressure Test 2: Sunk Cost + Exhaustion, Scenario, Your Options

### Community 353 - "Pressure Test 3: Authority + Social Pressure"
Cohesion: 0.40
Nodes (4): Choose A, B, or C, Pressure Test 3: Authority + Social Pressure, Scenario, Your Options

### Community 354 - "anthropic-best-practices.md"
Cohesion: 0.40
Nodes (4): [Analysis Title], Executive summary, Key findings, Recommendations

### Community 355 - "Anti-Patterns"
Cohesion: 0.40
Nodes (5): Anti-Patterns, ❌ Code in Flowcharts, ❌ Generic Labels, ❌ Multi-Language Dilution, ❌ Narrative Example

### Community 356 - "Testing All Skill Types"
Cohesion: 0.40
Nodes (5): Discipline-Enforcing Skills (rules/requirements), Pattern Skills (mental models), Reference Skills (documentation/APIs), Technique Skills (how-to guides), Testing All Skill Types

### Community 357 - "RED-GREEN-REFACTOR for Skills"
Cohesion: 0.40
Nodes (5): GREEN: Write Minimal Skill, Micro-Test Wording Before Full Scenarios, RED-GREEN-REFACTOR for Skills, RED: Write Failing Test (Baseline), REFACTOR: Close Loopholes

### Community 359 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 360 - "require"
Cohesion: 0.40
Nodes (5): require, laravel/framework, laravel/tinker, php, sendgrid/sendgrid

### Community 363 - "Pi Tool Mapping"
Cohesion: 0.50
Nodes (3): Pi Tool Mapping, Subagents, Task lists

### Community 364 - "Evaluation and iteration"
Cohesion: 0.50
Nodes (4): Build evaluations first, Develop Skills iteratively with the agent, Evaluation and iteration, Observe how agents navigate Skills

### Community 365 - "Checklist for effective Skills"
Cohesion: 0.50
Nodes (4): Checklist for effective Skills, Code and scripts, Core quality, Testing

### Community 366 - "Core principles"
Cohesion: 0.50
Nodes (4): Concise is key, Core principles, Set appropriate degrees of freedom, Test with all models you plan to use

### Community 367 - "File Organization"
Cohesion: 0.50
Nodes (4): File Organization, Self-Contained Skill, Skill with Heavy Reference, Skill with Reusable Tool

### Community 368 - "Skill Types"
Cohesion: 0.50
Nodes (4): Pattern, Reference, Skill Types, Technique

### Community 369 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 373 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **819 isolated node(s):** `Anti-Pattern: "This Is Too Simple To Need A Design"`, `Checklist`, `Process Flow`, `The Process`, `After the Design` (+814 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **57 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Controller` connect `SiteFooter.php` to `Page`, `Taxonomy`, `View`, `BlockRegistry`, `PackageList.php`, `PackagePostSlot.php`, `TravelDeals.php`, `2026_07_10_185347_create_posts_table.php`, `require-dev`, `User`, `artisan`, `BlogList.php`, `Widget`, `CampaignController.php`, `blog-list.blade.php`, `SettingsController.php`, `AdminUserController`, `site-footer.blade.php`, `PagesWidget`, `CollectionEntryController.php`, `@codemirror/lang-json`?**
  _High betweenness centrality (0.031) - this node is a cross-community bridge._
- **Why does `Block` connect `Block` to `UpdatesListWidget`, `BlockRegistry`, `Field.php`, `WhyChooseUs.php`, `ClientTestimonials.php`, `FeatureImageCards.php`, `WhyChooseUs.php`, `Advanced Query Patterns`, `Database Performance Best Practices`, `2026_07_10_185347_create_posts_table.php`, `FormEntry`, `BlogList.php`, `LatestBlog.php`, `PageBanner.php`, `SimpleText.php`, `WhyChooseUs.php`, `SiteNavbar.php`, `ProfileBento.php`, `UserController`?**
  _High betweenness centrality (0.025) - this node is a cross-community bridge._
- **Why does `Admin` connect `TeamCards.php` to `SiteNavbar.php`, `Model`, `2026_07_10_185347_create_posts_table.php`, `Taxonomy`, `AdminUserController`, `BlockRegistry`, `Post.php`, `Seeder`, `Collection.php`, `CollectionEntryController.php`, `User`, `artisan`, `PackageList.php`, `Widget`, `@codemirror/lang-json`?**
  _High betweenness centrality (0.020) - this node is a cross-community bridge._
- **What connects `Anti-Pattern: "This Is Too Simple To Need A Design"`, `Checklist`, `Process Flow` to the rest of the system?**
  _819 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `dependencies` be split into smaller, more focused modules?**
  _Cohesion score 0.09523809523809523 - nodes in this community are weakly interconnected._
- **Should `Layout` be split into smaller, more focused modules?**
  _Cohesion score 0.07407407407407407 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._