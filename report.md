# ICS Legal CRM — Performance Audit Report

**Date**: 20 July 2026  
**Audited by**: Lighthouse + Chrome DevTools Performance Trace  
**Environment**: Desktop (1x CPU, no network throttling)

---

## Pages Audited

| Page | URL | LCP | Key Issue |
|------|-----|-----|-----------|
| Firm Dashboard | `/dashboard.php` | **4,971 ms** ❌ | Render delay 4,451ms (89.5% of LCP) + slow PHP endpoints |
| Matter File | `/add_edit_case.php?caseid=121616` | **997 ms** ⚠️ | 46 render-blocking resources, CKEditor 1.8MB |
| Digital Whiteboard | `/my_workspace.php` | ~1,500 ms (est) | Same bloated base as dashboard + extra JS |

---

## Site Architecture Overview

- **CMS**: Hybrid CRM / LEGAL CRM by ICS Legal (custom PHP/jQuery, ~2018-2020 era)
- **Developed by**: Tech ICS
- **Pattern**: Legacy monolithic PHP with jQuery AJAX — every page loads the full app
- **Double-load bug**: Every page triggers a PHP sidecar that reloads all assets twice (~276 total requests per navigation)

### Resource Duplication Found

| Library | Occurrences |
|---------|-------------|
| Font Awesome | v4.7.0 AND v6.5.0 loaded together on some pages |
| Remixicon | v3.5.0 AND v4.6.0 on the same page |
| Rich text editors | CKEditor 4 + TinyMCE + Quill 2 on matter file page |
| Calendar widgets | jQuery UI datepicker + zabuto_calendar |

---

## Lighthouse Scores (Dashboard)

| Category | Score | Rating |
|----------|-------|--------|
| Best Practices | **100** | 🟢 Excellent |
| SEO | **91** | 🟢 Good |
| Accessibility | **65** | 🟡 Needs improvement |
| Performance | N/A | ⚪ Audits skipped (page complexity too high) |

---

## 🔴 Critical Issues

### 1. Render Delay Eats 89.5% of LCP (Dashboard)

```
LCP: 4,971 ms
├─ TTFB:          520 ms (10.5%)  — acceptable
└─ Render delay: 4,451 ms (89.5%) — browser stuck processing CSS/JS
```

The LCP element is a plain text `<p>` node. The browser has the HTML but can't paint it because it's busy downloading and parsing 46+ CSS/JS files.

### 2. 46+ CSS/JS Files Are Render-Blocking

Every page loads the full asset suite regardless of what it needs:

| Page | CSS files | JS files | Total render-blocking |
|------|-----------|----------|-----------------------|
| Dashboard | 24 (all render-blocking) | 25+ | **~49** |
| Matter File | 20 (all render-blocking) | 26 | **~46** |
| Digital Whiteboard | 24 (all render-blocking) | 30+ | **~54** |

**Estimated savings from fixing**: **1,839 ms** on LCP/FCP (matter file trace).

### 3. Zero Cache on Local Assets

Every asset from `cms.icslegal.com` has **Cache TTL: 0 seconds**:

```
/css/style.css                     → no-cache
/js/js_functions.js                → no-cache
/dashboard_assets/css/dashforge.css → no-cache
/dashboard_assets/js/jquery.min.js → no-cache
/img/logo.png                      → no-cache
/fonts/inter-ui/*.woff2            → no-cache
```

This means on every page navigation, every CSS, JS, font, and image re-downloads from scratch. CDN-hosted assets (jsdelivr, cdnjs, Cloudflare) have proper caching — but local server assets have **none**.

### 4. Slow PHP Backend Endpoints

```
Endpoint                                          Duration
──────────────────────────────────────────────────────────
raised-enquiry-firm-dashboard.php                 5,190 ms  ← longest chain
dashboard_module.php                               4,924 ms
workflow-management-firm-dashboard.php             4,958 ms
get_ics_legal_help_guide.php                       2,263 ms
get_help_and_resource.php                          1,192 ms
getting_user_notifications.php?userId=114          1,149 ms
```

These 6 endpoints load in series, totaling ~19 seconds of backend processing on the dashboard.

### 5. Page Double-Load Bug

Every page navigation triggers a PHP sidecar that re-requests the entire page. First load: ~138 requests. After sidecar triggers: another ~138 requests for the same assets. Total: **~276 requests per navigation**.

### 6. Third-Party Bloat

```
Provider                Transfer Size
─────────────────────────────────────
ckeditor.com            1,800 kB
JSDelivr CDN            1,400 kB
Cloudflare CDN            753 kB
Google Fonts              311 kB
Bootstrap CDN             244 kB
datatables.net            125 kB
jQuery CDN                121 kB
```

---

## 🟡 Moderate Issues

### 7. No Preconnect Hints

Missing `<link rel="preconnect">` for all CDNs except Google Fonts:
- `cdnjs.cloudflare.com`
- `cdn.jsdelivr.net`
- `cdn.ckeditor.com`
- `stackpath.bootstrapcdn.com`
- `unpkg.com`

### 8. Font Display: FOIT on Inter UI

`font-display: auto` on InterUI-Regular.woff2 causes invisible text while the font downloads.

### 9. CLS: 0.037 from Unsized GIF

`automation_loader.gif` has no `width`/`height` attributes, causing a layout shift at ~5s after page load.

### 10. Features Loaded Globally (Should Be Per-Page)

- DataTables (`jquery.dataTables.min.js`) — on every page
- SmartWizard — on every page
- Bootstrap Tagsinput — on every page
- zabuto_calendar — on every page
- Email module features (`mail.js`, `email-checker.js`, `email-v2.js`) — on non-email pages
- Flot charts — on every page
- Feather icons — on every page
- Chart.js 2.9.4 — on every page

---

## Top 10 Recommended Fixes

| # | Fix | Expected Gain | Effort |
|---|-----|---------------|--------|
| 1 | **Add `Cache-Control: public, max-age=31536000`** for all `cms.icslegal.com` static paths (`/dashboard_assets/`, `/js/`, `/css/`, `/img/`) via nginx | Instant repeat-visit speedup | **Low** |
| 2 | **Bundle CSS**: Merge 20 local CSS files into 1-2 bundles | -500ms render delay | **Low** |
| 3 | **Bundle JS**: Merge 25+ local JS into 2-3 bundles | -300ms render delay | **Low** |
| 4 | **Defer non-critical JS**: Move DataTables, SmartWizard, tagsinput, calendar, email features to `defer` or dynamic import | -1,200ms render delay | **Medium** |
| 5 | **Fix slowest PHP endpoints**: Profile and optimize queries in `dashboard_module.php` and `raised-enquiry-firm-dashboard.php` (add DB indexes, query caching, materialized views) | -5,000ms on dashboard | **High** |
| 6 | **Fix double page load**: Remove the PHP sidecar that re-fetches all assets | -50% of ~276 total requests | **Medium** |
| 7 | **Deduplicate libraries**: Remove duplicate Font Awesome (keep v6 only), duplicate Remixicon (keep v4 only), choose one editor (keep Quill 2 or CKEditor) | -500KB per page | **Low** |
| 8 | **Set `font-display: swap`** on InterUI font in CSS `@font-face` declaration | Eliminate FOIT | **Low** |
| 9 | **Add preconnect hints** for top CDN origins: `<link rel="preconnect" href="https://cdnjs.cloudflare.com">`, `<link rel="preconnect" href="https://cdn.jsdelivr.net">`, etc. | -200ms connection time | **Low** |
| 10 | **Add `width` and `height`** attributes to `automation_loader.gif` | Fix 0.037 CLS | **Low** |

---

## Quick Wins (Can Be Done in < 1 Hour)

1. **nginx cache headers** — add to server config, repeat visits instantly faster
2. **font-display: swap** — one CSS change
3. **preconnect hints** — add `<link>` tags in `<head>`
4. **Unused library cleanup** — remove the duplicate icon/editor versions
5. **Image sizing** — add `width`/`height` to the loader GIF
