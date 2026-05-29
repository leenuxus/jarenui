# Changelog

All notable changes to `jarenui/livewire` will be documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Planned
- `Autocomplete` searchable input with async results
- `DateRangePicker` with calendar popup
- `ColorPicker` with hex/rgb/hsl inputs
- `CommandPalette` global ⌘K command menu
- `DataGrid` with editable cells and virtual scroll
- `RichEditor` Tiptap/ProseMirror integration

---

## [1.0.0] — 2026-05-29

### Added

**50 Blade components** across 8 categories:

#### Primitives
- `Button` — 6 variants (primary, secondary, ghost, danger, success, warning), 3 sizes, icon support, loading state
- `Badge` — 7 colour variants, dot indicator, dismiss button
- `Avatar` — 5 sizes, 6 gradients, auto-colour from name, status dot, stack group
- `Brand` — logo + name with dot icon, optional badge
- `Heading` — semantic h1–h6 with visual size override
- `Text` — paragraph with size/colour/weight props
- `Separator` — horizontal, vertical, and labelled variants

#### Form
- `Input` — with icon, prefix/suffix, clearable, copyable, error state
- `Textarea` — resize modes, auto-resize, character counter
- `Select` — native and searchable custom dropdown, multi-select, option groups
- `Checkbox` — indeterminate state, card variant
- `RadioGroup` + `Radio` — card variant with badge and price display
- `Switch` — with label/description, card variant, alignment
- `Slider` — accessible range with fill bar
- `OtpInput` — configurable digit count, separator, paste support
- `Pillbox` — tag input with suggestions autocomplete
- `Field` — label/hint/error wrapper for any control

#### Navigation
- `Accordion` + `Accordion.Item` — single/multiple open, animated collapse
- `Breadcrumbs` — items prop or slot, 3 separator styles, home icon
- `Dropdown` + `Dropdown.Item|Separator|Group` — keyboard shortcuts display, danger variant
- `Navbar` + `Navbar.Item` — auto-active detection, badge, external link
- `Pagination` — works with Livewire paginator or manual props, page window
- `Tabs` + `Tabs.Tab|Panel` — line/pill/box variants, keyboard nav, badge

#### Overlay
- `Modal` — named modals, open/close via events, danger accent, all sizes
- `Tooltip` — 4 positions, configurable delay, rich HTML
- `Popover` — 8 positions, close-on-click

#### Feedback
- `Toast` (Livewire) — stacked, auto-dismiss progress bar, action button, `HasToast` trait
- `Callout` — 4 types (info/success/warning/danger), dismissible
- `Progress` — linear (4 colours, 4 sizes) and circular SVG ring, step indicator
- `Skeleton` — 5 variants (text, avatar, card, table, form)

#### Display
- `Card` — title, description, media, footer slots, hover lift, link mode
- `Profile` — full card and compact row variants, stats, tags, verified badge
- `Timeline` + `Timeline.Item` — 4 status states, animated active indicator

#### Layout
- `Header` — sticky, backdrop blur, brand/nav/search/actions slots
- `Sidebar` + `Sidebar.Section|Item|User` — collapsible, badge counts, user footer
- `Kanban` (Livewire) — drag-and-drop (SortableJS), WIP limits, add/delete cards

**3 Livewire full-stack components:** `Toast`, `Table`, `Kanban`

**4 Artisan commands:** `jaren:install`, `jaren:publish`, `jaren:make-table`, `jaren:make-kanban`

**`jarenui` facade** with `theme()`, `accent()`, `renderStyles()`

**`HasToast` trait** with fluent builder: `$this->toast()->persistent()->action('Undo','undo')->danger('Deleted')`

**`jarenui.css`** — complete CSS variable token system:
- Light + dark mode
- 5 built-in brand themes (rose, violet, emerald, amber)
- 2 shape variants (sharp, rounded)
- System `prefers-color-scheme` fallback

**Test suite:** 40+ Pest tests covering all Livewire components and Blade rendering

**Auto-discovery** via `extra.laravel` in `composer.json`

**Multi-version CI** — PHP 8.1/8.2/8.3 × Laravel 10/11/12/13
