# Graph Report - lara-cms  (2026-07-16)

## Corpus Check
- 254 files · ~105,725 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 708 nodes · 956 edges · 143 communities (105 shown, 38 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 64 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `b9f530c9`
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

## God Nodes (most connected - your core abstractions)
1. `Block` - 49 edges
2. `Field` - 37 edges
3. `Layout` - 21 edges
4. `Post` - 21 edges
5. `Page` - 20 edges
6. `Controller` - 19 edges
7. `Sections` - 19 edges
8. `Package` - 17 edges
9. `Taxonomy` - 13 edges
10. `AssetsController` - 12 edges

## Surprising Connections (you probably didn't know these)
- `AboutIntro` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/AboutIntro.php → app/Blocks/Block.php
- `BlogPostSlot` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/BlogPostSlot.php → app/Blocks/Block.php
- `ClientTestimonials` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/ClientTestimonials.php → app/Blocks/Block.php
- `Contact` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/Contact.php → app/Blocks/Block.php
- `DestinationsGrid` --inherits--> `Block`  [EXTRACTED]
  app/Blocks/common/DestinationsGrid.php → app/Blocks/Block.php

## Import Cycles
- None detected.

## Communities (143 total, 38 thin omitted)

### Community 0 - "dependencies"
Cohesion: 0.05
Nodes (45): alpinejs, autoprefixer, concurrently, @fortawesome/fontawesome-free, laravel-vite-plugin, dependencies, alpinejs, sortablejs (+37 more)

### Community 1 - "Layout"
Cohesion: 0.19
Nodes (5): LayoutFactory, PackageFactory, PostFactory, UserFactory, Factory

### Community 2 - "composer.json"
Cohesion: 0.05
Nodes (42): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+34 more)

### Community 3 - "Page"
Cohesion: 0.09
Nodes (9): PageController, Request, Request, PreviewController, Page, BlockPreview, Sections, HomePageSeeder (+1 more)

### Community 4 - "Controller"
Cohesion: 0.08
Nodes (10): CommandSearchController, Request, PackageController, Request, PageController, Package, CommandSearch, Controller (+2 more)

### Community 5 - "Post"
Cohesion: 0.07
Nodes (27): APIs & Eloquent Resources, Application Structure & Architecture, Artisan, Conventions, Deployment, Do Things the Laravel Way, Documentation Files, Foundational Context (+19 more)

### Community 6 - "scripts"
Cohesion: 0.08
Nodes (27): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+19 more)

### Community 7 - "Model"
Cohesion: 0.28
Nodes (3): AssetsController, Request, Asset

### Community 8 - "LoginController.php"
Cohesion: 0.16
Nodes (6): ProfileController, LoginRequest, ProfileUpdateRequest, FormRequest, RedirectResponse, View

### Community 9 - "Taxonomy"
Cohesion: 0.06
Nodes (23): Request, PostController, Request, SettingsController, Request, TaxonomyController, LoginController, RedirectResponse (+15 more)

### Community 11 - "Block"
Cohesion: 0.22
Nodes (4): Block, BlogList, BlogSection, TravelDeals

### Community 12 - "command"
Cohesion: 0.10
Nodes (20): FIRECRAWL_API_KEY, command, env, type, command, enabled, type, mcp (+12 more)

### Community 14 - "tiptap.js"
Cohesion: 0.46
Nodes (6): mountTipTap(), ResizableImage, setupImageToolbar(), setupResizeHandle(), setupToolbarOverflow(), updateActiveButtons()

### Community 28 - "TeamCards.php"
Cohesion: 0.20
Nodes (3): PackageBooking, SimpleText, TeamCards

### Community 29 - "TravelDeals.php"
Cohesion: 0.08
Nodes (12): PackageAbout, PackageDetails, PackageFaq, PackageFeatures, PackageGalleryHero, PackageHero, PackageHighlights, PackageInfo (+4 more)

### Community 121 - "Seeder"
Cohesion: 0.11
Nodes (10): LayoutController, Request, Layout, User, Authenticatable, DatabaseSeeder, LayoutSeeder, UserSeeder (+2 more)

### Community 122 - "opencode.json"
Cohesion: 0.50
Nodes (3): plugin, $schema, .opencode/plugins/graphify.js

## Knowledge Gaps
- **120 isolated node(s):** `$schema`, `.opencode/plugins/graphify.js`, `$schema`, `superpowers@git+https://github.com/obra/superpowers.git`, `@dietrichgebert/ponytail` (+115 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Block` connect `Block` to `Taxonomy`, `BlockRegistry`, `Field.php`, `BlogPostSlot.php`, `ClientTestimonials.php`, `DestinationsGrid.php`, `FeatureImageCards.php`, `HeroBanner.php`, `LatestBlog.php`, `PackageList.php`, `PackagePostSlot.php`, `PageBanner.php`, `Contact.php`, `TeamCards.php`, `WhyChooseUs.php`, `SiteFooter.php`, `SiteNavbar.php`, `SiteTopBar.php`, `ProfileBento.php`?**
  _High betweenness centrality (0.124) - this node is a cross-community bridge._
- **Why does `Post` connect `Taxonomy` to `Layout`, `Page`, `Controller`, `Seeder`?**
  _High betweenness centrality (0.031) - this node is a cross-community bridge._
- **Why does `Layout` connect `Seeder` to `Taxonomy`, `Page`, `Controller`, `Layout`?**
  _High betweenness centrality (0.028) - this node is a cross-community bridge._
- **Are the 22 inferred relationships involving `Field` (e.g. with `.resolvedFields()` and `.fields()`) actually correct?**
  _`Field` has 22 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `.opencode/plugins/graphify.js`, `$schema` to the rest of the system?**
  _120 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `dependencies` be split into smaller, more focused modules?**
  _Cohesion score 0.0463768115942029 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.046511627906976744 - nodes in this community are weakly interconnected._