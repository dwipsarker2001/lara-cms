# Graph Report - .  (2026-07-16)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 581 nodes · 812 edges · 121 communities (97 shown, 24 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 69 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `6878855a`
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

## God Nodes (most connected - your core abstractions)
1. `Block` - 49 edges
2. `Field` - 37 edges
3. `Page` - 26 edges
4. `Controller` - 25 edges
5. `Layout` - 24 edges
6. `Post` - 24 edges
7. `Sections` - 19 edges
8. `Taxonomy` - 16 edges
9. `AssetsController` - 12 edges
10. `LayoutController` - 11 edges

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

## Communities (121 total, 24 thin omitted)

### Community 0 - "dependencies"
Cohesion: 0.05
Nodes (45): alpinejs, autoprefixer, concurrently, @fortawesome/fontawesome-free, laravel-vite-plugin, dependencies, alpinejs, sortablejs (+37 more)

### Community 1 - "Layout"
Cohesion: 0.08
Nodes (15): LayoutController, Request, Layout, User, Authenticatable, LayoutFactory, PostFactory, UserFactory (+7 more)

### Community 2 - "composer.json"
Cohesion: 0.05
Nodes (42): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+34 more)

### Community 3 - "Page"
Cohesion: 0.10
Nodes (7): PageController, Request, PageController, Page, Sections, HomePageSeeder, static

### Community 4 - "Controller"
Cohesion: 0.11
Nodes (13): CommandSearchController, Request, Request, PreviewController, RedirectResponse, View, ProfileController, Request (+5 more)

### Community 5 - "Post"
Cohesion: 0.11
Nodes (6): Request, PostController, BlogController, BelongsToMany, Post, CommandSearch

### Community 6 - "scripts"
Cohesion: 0.08
Nodes (27): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+19 more)

### Community 7 - "Model"
Cohesion: 0.16
Nodes (7): AssetsController, Request, Asset, Booking, Destination, Package, Model

### Community 8 - "LoginController.php"
Cohesion: 0.14
Nodes (7): LoginController, RedirectResponse, Request, View, LoginRequest, ProfileUpdateRequest, FormRequest

### Community 9 - "Taxonomy"
Cohesion: 0.17
Nodes (7): Request, TaxonomyController, Taxonomy, BelongsToMany, Term, BelongsTo, HasMany

### Community 11 - "Block"
Cohesion: 0.22
Nodes (4): Block, BlogList, BlogSection, SimpleText

### Community 12 - "command"
Cohesion: 0.15
Nodes (12): command, enabled, type, mcp, laravel-boost, plugin, $schema, artisan (+4 more)

### Community 14 - "tiptap.js"
Cohesion: 0.46
Nodes (6): mountTipTap(), ResizableImage, setupImageToolbar(), setupResizeHandle(), setupToolbarOverflow(), updateActiveButtons()

## Knowledge Gaps
- **78 isolated node(s):** `php`, `$schema`, `name`, `type`, `description` (+73 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **24 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Block` connect `Block` to `Page`, `BlockRegistry`, `Field.php`, `BlogPostSlot.php`, `ClientTestimonials.php`, `DestinationsGrid.php`, `FeatureImageCards.php`, `HeroBanner.php`, `LatestBlog.php`, `PackageList.php`, `PackagePostSlot.php`, `PageBanner.php`, `Contact.php`, `TeamCards.php`, `TravelDeals.php`, `WhyChooseUs.php`, `SiteFooter.php`, `SiteNavbar.php`, `SiteTopBar.php`?**
  _High betweenness centrality (0.121) - this node is a cross-community bridge._
- **Why does `Controller` connect `Controller` to `Layout`, `Page`, `Post`, `Model`, `LoginController.php`, `Taxonomy`?**
  _High betweenness centrality (0.038) - this node is a cross-community bridge._
- **Why does `Post` connect `Post` to `Layout`, `Controller`, `Taxonomy`, `Model`?**
  _High betweenness centrality (0.034) - this node is a cross-community bridge._
- **Are the 22 inferred relationships involving `Field` (e.g. with `.resolvedFields()` and `.fields()`) actually correct?**
  _`Field` has 22 INFERRED edges - model-reasoned connections that need verification._
- **What connects `php`, `$schema`, `name` to the rest of the system?**
  _78 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `dependencies` be split into smaller, more focused modules?**
  _Cohesion score 0.0463768115942029 - nodes in this community are weakly interconnected._
- **Should `Layout` be split into smaller, more focused modules?**
  _Cohesion score 0.07610993657505286 - nodes in this community are weakly interconnected._