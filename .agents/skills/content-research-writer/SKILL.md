---
name: content-research-writer
description: Assists in writing high-quality CMS content and web copy by conducting topic research, improving hooks, structuring outlines, and providing real-time section-by-section copywriting tailored to Lara-CMS blocks.
---

# Content Research Writer for Lara-CMS

This skill equips the AI Agent to act as a world-class collaborative research partner and copywriter for Lara-CMS websites, collections, and landing pages.

## Core Capabilities in Lara-CMS

1. **Compelling Hook Generation**: Analyzes headlines and generates 3 high-impact angles (Data-Driven, Narrative/Story, and Bold Transformation).
2. **Research & Value Synthesis**: Identifies key facts, value propositions, and credible evidence to support claims.
3. **Section-by-Section Block Copywriting**: Writes tailored copy for each specific block (Hero, Features, Testimonials, FAQ, Story) without adding unnecessary sections.
4. **Voice Preservation**: Calibrates tone (Conversion-focused, Luxury/Bespoke, Modern/Punchy, Warm Storyteller) while keeping the brand's unique identity.
5. **Iterative Polish & Flow**: Reviews clarity, transitions, and calls-to-action before applying live typewriter updates to the editor.

---

## Hook Formulation Matrix for Web Blocks

When optimizing or writing headlines for any CMS section, consider 3 distinct psychological angles:

| Angle | Strategy | Example for Travel/Hospitality |
| :--- | :--- | :--- |
| **Option 1: Bold Transformation** | Focuses on the ultimate aspirational outcome. | *"Experience the World Before the World Catches Up."* |
| **Option 2: Curiosity / Story** | Triggers imagination and emotional intrigue. | *"Behind Every Uncharted Trail Lies a Story Waiting to Be Yours."* |
| **Option 3: Specific & Data-Backed** | Delivers concrete proof and immediate clarity. | *"Over 500+ Handpicked Stays. Zero Hidden Fees. 100% Curated."* |

---

## Block-by-Block Writing Guidelines

### 1. Hero Banners & Page Headers
- **Badge**: 2–4 words establishing credibility or category (e.g., *"Curated Expeditions"*).
- **Headline**: 5–9 words delivering the primary hook or promise.
- **Description**: 15–25 words describing the experience, removing friction, and framing the value.
- **CTA**: Action-oriented verb phrase (e.g., *"Explore Curated Itineraries"*).

### 2. Feature Grids & Value Pillars
- **Title**: Action or benefit-oriented (e.g., *"Bespoke Itineraries"* rather than *"Planning"*).
- **Description**: 12–20 words focused on customer outcomes rather than internal process.

### 3. Testimonials & Social Proof
- **Quote**: Specific praise highlighting overcoming a doubt or an extraordinary moment.
- **Role/Handle**: Realistic persona representing the target demographic.

### 4. FAQs & Objection Handling
- **Question**: Real customer concerns (cancellations, pricing, guides, safety).
- **Answer**: Direct, transparent, and confidence-building.

---

## Integration with Editor Actions

- **Content Writing Mode**: Use `update_field` or `update_section` to modify copy in place with live typewriter animation. Never add new blocks for writing requests.
- **Full Page Generation**: When explicitly requested to build/create a page, assemble cohesive blocks using `replace_all_sections` or `add_section` and populate with researched copy.
