# Design Document: Asset Picker Panel Fixes & UI Improvements

## Overview
This document outlines the bug fixes and UI/UX enhancements for the slide-over Asset Picker panel (`resources/views/admin/components/asset-picker.blade.php`).

## Identified Issues & Fixes

### 1. Header & Icons
- **Issue**: The "Create Folder" button in the panel header uses a "User Plus" icon.
- **Fix**: Replace the SVG with a standard "Folder Plus" icon.

### 2. Search & Filter Bar
- **Issue**: No search bar exists in the asset picker panel.
- **Fix**: Add a search input below the header with real-time filtering by asset/folder name, including a clear search button (`searchQuery`).

### 3. Breadcrumb Navigation Pathing
- **Issue**: `get breadcrumbs()` prepends `assets/` to directory paths (`crumb.path = 'assets/' + acc`), causing `setDirectory('assets/...')` to send an incorrect `directory` parameter to the API.
- **Fix**: Update `get breadcrumbs()` to compute relative paths matching `directory_path` stored in the system (e.g. `'New Folder'`).

### 4. Non-Image File Thumbnails
- **Issue**: Non-image files (PDF, ZIP, DOCX, etc.) render inside an `<img>` tag and fail, showing an empty grey box.
- **Fix**: Check `mime_type` or extension. If not an image, display a file type icon badge (Document / Archive SVG) with file extension label.

### 5. Inline Renaming UX
- **Issue**: Renaming card text strips padding and border, causing layout jump. Enter key blur event causes duplicate submit calls.
- **Fix**: Keep consistent card padding/border, add focus ring to the rename input, and prevent double submission by resetting `renamingId` cleanly.

### 6. Panel Width & Tooltips
- **Issue**: `max-w-[400px]` is too narrow for 3 grid columns; filenames get truncated without hover tooltips.
- **Fix**: Expand panel width to `max-w-[480px]` and add `title="item.name"` to card text elements for full name inspection on hover.

### 7. Drag & Drop Feedback
- **Issue**: Lack of clear visual indicator when hovering a card over a folder item.
- **Fix**: Add active border/background highlight state (`bg-primary/10 border-primary ring-2 ring-primary/40`) when `dragTargetId === item.id`.
