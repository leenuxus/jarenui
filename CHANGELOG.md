# Changelog

All notable changes to `jarenui/livewire` will be documented in this file.

---

## [1.3.0] — 2026-06-01

### Added

**`JarenUI\Livewire\Wizard`** — abstract base class for multi-step form wizards:

- **Step definitions** — `$steps` array with `id`, `label`, and optional `icon` per step
- **Three stepper variants:** `default` (numbered dots + connecting line), `numbered` (same), `minimal` (expanding dot pills)
- **Three sizes:** `sm`, `md`, `lg` — scales typography, padding, and dot dimensions
- **Progress bar** — optional linear bar beneath the stepper (`show-progress`)
- **Clickable back-navigation** — clicking a completed step dot jumps back to it (`clickable`)
- **Per-step validation** — `$stepRules` array keyed by step id; runs Laravel's `validate()` automatically on `next()`
- **Lifecycle hooks** — `onStepLeaving()`, `onStepEntering()`, `onCancel()`
- **Submit hook** — `submit()` called on the final step's Continue press; call `complete()` to mark done
- **`completedData()`** — return array attached to `jaren-wizard-completed` event
- **Named slots** — pass `$complete` slot to replace the default success screen; pass `$cancelButton` to add a cancel link in the footer
- **Loading state** — Continue button shows spinner and is disabled during Livewire request
- **Error summary** — validation errors collected from all rules shown in a danger callout inside the step body
- **Aria / accessibility** — `role="region"`, `aria-current="step"` on active step, `role="progressbar"` on progress bar, `role="tabpanel"` on step panels

**`php artisan jaren:make-wizard`** — scaffolding command:
- Generates PHP class extending `JarenUI\Livewire\Wizard`
- Generates per-step Blade partials in `resources/views/livewire/{name}/`
- `--steps=` option: comma-separated step ids
- `--variant=` option: `default|numbered|minimal`
- `--size=` option: `sm|md|lg`
- `--force` to overwrite

**Events dispatched:**
- `jaren-wizard-step-changed` — `{step, index}` — any navigation
- `jaren-wizard-completed` — `{data}` — on `complete()`
- `jaren-wizard-cancelled` — on `cancel()`

**Blade view** (`resources/views/components/jaren/wizard.blade.php`):
- Stepper bar with done/active/pending states and transition colours
- Step body with per-step slot/method rendering
- Footer with Back + Continue/Submit buttons and step counter
- Completed and cancelled state panels


---

## [1.1.2] — 2026-05-31

### Added

- **`toggleable` prop on `<x-jaren::input>`** — password inputs now support a show/hide toggle button in the trailing slot. Set `type="password" toggleable` and the eye/eye-slash icon handles the rest. Alpine manages the input type reactively; the `name` attribute and Livewire `wire:model` are unaffected.

---

## [1.1.0] — 2026-06-01

### Added

**`<livewire:jaren.event-calendar/>`** — Full-featured Livewire event calendar component:

**Three views:**
- `month` — grid of days, events shown as coloured pills (max 3 per day + "+N more" overflow that drills into day view)
- `week` — 7-column time grid with timed events positioned and sized by duration
- `day` — single-day time grid with full-height events, current-time indicator (red line)

**Toolbar:** prev/next navigation, Today button, view switcher — all wired to Livewire

**Event detail panel:** slides in below the calendar when an event is selected; shows title, time, description, and optional URL link

**`creatable` mode:** clicking an empty date dispatches `jaren-event-created` — hook into a modal or redirect

**Time grid:** configurable `dayStartHour`/`dayEndHour` (default 7 am – 8 pm); each hour slot is 56px; events positioned absolutely by start time and sized by duration

**Current-time indicator:** red dot + line in day view showing exact current time

**Locale support:** `locale` prop controls `isoFormat()` strings for month/day names

**`startDay`** prop: 0 = Sunday, 1 = Monday — affects week grid and month grid padding

**Subclass API:**
- Override `fetchEvents(Carbon $from, Carbon $to): array` to load from any database
- Auto-called when view or period changes
- Returns `CalendarEvent[]`

**9 event colours:** `blue`, `green`, `amber`, `red`, `purple`, `teal`, `pink`, `coral`, `gray`

**Dispatched events:** `jaren-event-selected`, `jaren-event-created`, `jaren-event-moved`, `jaren-view-changed`, `jaren-date-clicked`

**`JarenUI\CalendarEvent`** — value object for calendar events:
- Constructor: `new CalendarEvent(id, title, start, end, color, description, url, allDay, meta)`
- `CalendarEvent::from($model, startKey, endKey, ...)` — cast from any Eloquent model or array
- `CalendarEvent::fromCollection($models, ...)` — cast an entire collection at once
- Accessors: `date()`, `startTime()`, `endTime()`, `durationMinutes()`, `spansMultipleDays()`, `toArray()`

**`php artisan jaren:make-event-calendar`** — scaffolding command:
- `--model=Meeting` auto-generates the `fetchEvents()` body with correct key mapping
- Produces a ready-to-use subclass in `app/Livewire/`

**30 new tests** in `tests/Feature/EventCalendarTest.php`:
- `CalendarEvent` construction, mapping, collection casting, `toArray()`
- `EventCalendar` all views, navigation (month/week/day), event selection, dismissal, drill-down
- `fetchEvents` subclass override
- Event dispatching for all interactions


**`<x-jaren::calendar>`** — Full-featured calendar Blade component:
- **Three selection modes:** `single` (default), `multiple` (non-consecutive), `range` (start/end span)
- **Six sizes:** `xs`, `sm`, `base`, `lg`, `xl`, `2xl` — controlled via CSS custom property `--jaren-cell`
- **Date constraints:** `min`, `max`, and `unavailable` props (all accept `Y-m-d` strings; `min`/`max` accept `'today'` shorthand)
- **Range controls:** `min-range` and `max-range` props limit selectable span in days
- **Multi-month display:** `months` prop renders side-by-side panels (default 2 for range mode)
- **Locale-aware:** day names, month names, and week start day follow `locale` prop or browser `navigator.language`
- **`start-day`:** override first day of week (0 = Sunday … 6 = Saturday)
- **`with-today`:** footer shortcut button to jump to and select today
- **`selectable-header`:** clicking month/year name opens a month-grid picker for fast navigation
- **`fixed-weeks`:** always render 6 rows to prevent layout shift
- **`week-numbers`:** ISO 8601 week numbers in a left column
- **`open-to` / `force-open-to`:** control which month the calendar opens to
- **`static`:** display-only mode — no interaction, no cursors
- **`navigation`:** hide prev/next arrows for embedded display
- **`jaren-calendar-change` event:** dispatched on every selection change with `detail.value`
- **`wire:model` support:** full Livewire binding for all three modes
- **Dark mode** via `[data-theme="dark"]` and `.dark` class

**`JarenUI\DateRange`** — CarbonPeriod value object for range mode:
- `new DateRange($start, $end)` — construct from any Carbon-parseable value
- `DateRange::fromString('Y-m-d/Y-m-d')` — parse wire:model string format
- `DateRange::fromArray(['start'=>'…','end'=>'…'])` — parse array format
- `start()` / `end()` — Carbon accessors
- `length()` — inclusive day count
- `contains($date)` — boundary-inclusive check
- `toArray()` — Carbon[] of every day in range
- `toLivewire()` / `fromLivewire()` — Livewire Wireable contract
- `forWhereBetween()` — `[Carbon, Carbon]` tuple for Eloquent `whereBetween()`
- Iterable as `CarbonPeriod` — `foreach ($range as $day) { … }`
- Stringable — `(string) $range` returns `'Y-m-d/Y-m-d'`
- Works with `#[Session]` attribute for session persistence

**`JarenUI\Livewire\Casts\DateRangeSynth`** — Livewire property synthesizer:
- Registers automatically — no manual cast needed
- Handles hydration/dehydration of `?DateRange` properties
- Works with `wire:model`, `wire:model.live`, and `#[Session]`

**24 new tests** in `tests/Feature/CalendarTest.php` covering:
- DateRange construction, accessors, serialization, Eloquent helpers, iteration
- Calendar Blade rendering for all modes, sizes, and prop combinations

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

**`JarenUI` facade** with `theme()`, `accent()`, `renderStyles()`

**`HasToast` trait** with fluent builder: `$this->toast()->persistent()->action('Undo','undo')->danger('Deleted')`

**`jarenui.css`** — complete CSS variable token system:
- Light + dark mode
- 5 built-in brand themes (rose, violet, emerald, amber)
- 2 shape variants (sharp, rounded)
- System `prefers-color-scheme` fallback

**Test suite:** 40+ Pest tests covering all Livewire components and Blade rendering

**Auto-discovery** via `extra.laravel` in `composer.json`
