# JarenUI for Laravel Livewire

[![Latest Version](https://img.shields.io/packagist/v/leenuxus/jarenui.svg)](https://packagist.org/packages/leenuxus/jarenui)
[![PHP Version](https://img.shields.io/packagist/php-v/leenuxus/jarenui.svg)](https://packagist.org/packages/leenuxus/jarenui)
[![License](https://img.shields.io/github/license/leenuxus/jarenui)](LICENSE.md)

**50+ production-ready Livewire components** — dark mode, CSS-variable theming, Alpine.js interactivity, full ARIA accessibility, and zero Tailwind config required.

---

## Requirements

| Dependency     | Version        |
|----------------|----------------|
| PHP            | `^8.4`         |
| Laravel        | `^13`          |
| Livewire       | `^4.0`         |
| Alpine.js      | `^3.0`         |
| Tailwind CSS   | `^4.0` (optional — all styling uses CSS variables) |

---

## Installation

```bash
composer require leenuxus/jarenui
php artisan jaren:install
```

That's it. The installer:
- Publishes `config/jarenui.php`
- Injects `@jarenStyles` into your layout `<head>`
- Injects `<livewire:jaren.toast/>` before your `</body>`

### Manual setup

If you prefer not to use the installer:

```blade
{{-- In your layout <head> --}}
@jarenStyles

{{-- Before </body> --}}
<livewire:jaren.toast/>
```

### Alpine.js

JarenUI uses Alpine.js for interactivity. Load it in your layout or `app.js`:

```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

---

## Quick start

```blade
{{-- Button --}}
<x-jaren::button>Save changes</x-jaren::button>
<x-jaren::button variant="danger" icon="trash" wire:click="delete">Delete</x-jaren::button>

{{-- Input --}}
<x-jaren::input label="Email" icon="envelope" wire:model="email"/>

{{-- Select (searchable) --}}
<x-jaren::select label="Role" :options="$roles" searchable wire:model="role"/>

{{-- Accordion --}}
<x-jaren::accordion>
    <x-jaren::accordion.item title="What is jarenui?">
        A Livewire component library…
    </x-jaren::accordion.item>
</x-jaren::accordion>

{{-- Tabs --}}
<x-jaren::tabs default="overview">
    <x-jaren::tabs.tab name="overview">Overview</x-jaren::tabs.tab>
    <x-jaren::tabs.tab name="settings">Settings</x-jaren::tabs.tab>
    <x-jaren::tabs.panel name="overview">Overview content…</x-jaren::tabs.panel>
    <x-jaren::tabs.panel name="settings">Settings content…</x-jaren::tabs.panel>
</x-jaren::tabs>

{{-- Table (extend the base class) --}}
<livewire:users-table/>

{{-- Kanban --}}
<livewire:project-kanban/>

{{-- Toast from Livewire PHP --}}
$this->dispatch('jaren-toast', type: 'success', title: 'Saved!');
```

---

## Artisan commands

| Command | Description |
|---|---|
| `php artisan jaren:install` | Install jarenui (publish assets, inject into layout) |
| `php artisan jaren:publish --views` | Publish Blade views for customisation |
| `php artisan jaren:publish --config` | Publish config file |
| `php artisan jaren:publish --assets` | Publish CSS to `public/vendor/jarenui/` |
| `php artisan jaren:publish --stubs` | Publish layout stub |
| `php artisan jaren:make-table UsersTable --model=User` | Generate a Table component |
| `php artisan jaren:make-kanban ProjectKanban` | Generate a Kanban component |

---

## Theming

All visual tokens are CSS custom properties. Override any in your own CSS — no Tailwind config or build step needed:

```css
/* resources/css/app.css */
:root {
    --accent:      #7c3aed;    /* Brand primary */
    --accent-bg:   #f5f3ff;
    --accent-text: #6d28d9;
    --radius:      8px;        /* Corner radius */
}
```

Or via `.env` / `config/jarenui.php`:

```bash
jarenui_ACCENT=#7c3aed
jarenui_THEME=violet
```

### Built-in themes

Add a class to `<html>` to activate a named theme:

| Class | Colour |
|---|---|
| `theme-rose` | Rose / pink |
| `theme-violet` | Violet / purple |
| `theme-emerald` | Emerald green |
| `theme-amber` | Amber / gold |
| `theme-sharp` | Reduced border radii |
| `theme-rounded` | Increased border radii |

### Dark mode

Set `data-theme="dark"` on `<html>` (or use Tailwind's `.dark` class):

```blade
<html data-theme="{{ auth()->user()?->dark_mode ? 'dark' : 'light' }}">
```

Toggle dynamically with Alpine:

```html
<button @click="
    const d = document.documentElement;
    d.setAttribute('data-theme', d.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
">Toggle dark</button>
```

---

## Component reference

### Layouts

**`<x-jaren::header>`** — Sticky app header with brand, nav, search, and actions slots.

**`<x-jaren::sidebar>`** — Collapsible sidebar with sections, items, and user footer.

**`<x-jaren::navbar>`** — Standalone horizontal nav bar.

---

### Primitives

**`<x-jaren::button>`**
```blade
<x-jaren::button variant="primary|secondary|ghost|danger|success|warning" size="sm|md|lg"
    icon="HEROICON" icon-end="HEROICON" :loading="bool" :disabled="bool" href="URL">
    Label
</x-jaren::button>
```

**`<x-jaren::badge>`**
```blade
<x-jaren::badge color="blue|green|red|yellow|gray|purple|pink" dot close>Text</x-jaren::badge>
```

**`<x-jaren::avatar>`**
```blade
<x-jaren::avatar name="Jane Doe" src="/photo.jpg" size="xs|sm|md|lg|xl"
    color="blue|auto" status="online|away|busy|offline" shape="circle|square"/>
```

**`<x-jaren::brand>`**
```blade
<x-jaren::brand name="Acme" src="/logo.svg" href="/" dot badge="Beta"/>
```

---

### Form

**`<x-jaren::input>`**
```blade
<x-jaren::input label="Name" hint="Your full name" error="Required"
    icon="user" icon-end="check" prefix="$" suffix=".com"
    clearable copyable size="sm|md|lg" wire:model="name"/>
```

**`<x-jaren::textarea>`**
```blade
<x-jaren::textarea label="Bio" :rows="4" resize="none|vertical|both"
    :auto-resize="true" :max-length="500" show-count wire:model="bio"/>
```

**`<x-jaren::select>`**
```blade
<x-jaren::select label="Role" :options="['dev'=>'Developer']"
    searchable clearable wire:model="role"/>
```

**`<x-jaren::checkbox>`**
```blade
<x-jaren::checkbox label="Accept terms" description="You agree to our ToS"
    :indeterminate="bool" card wire:model="terms"/>
```

**`<x-jaren::radio-group>` + `<x-jaren::radio>`**
```blade
<x-jaren::radio-group label="Plan" variant="card" wire:model="plan">
    <x-jaren::radio value="free"  label="Free"  description="Up to 3 projects" price="$0"/>
    <x-jaren::radio value="pro"   label="Pro"   description="Unlimited"         price="$12/mo" badge="Popular"/>
</x-jaren::radio-group>
```

**`<x-jaren::switch>`**
```blade
<x-jaren::switch label="Dark mode" description="Switch theme" align="left|right" card wire:model="dark"/>
```

**`<x-jaren::slider>`**
```blade
<x-jaren::slider label="Volume" :min="0" :max="100" :step="1" prefix="" suffix="%" wire:model="volume"/>
```

**`<x-jaren::otp-input>`**
```blade
<x-jaren::otp-input :digits="6" :separator="3" wire:model="code"/>
```

**`<x-jaren::pillbox>`**
```blade
<x-jaren::pillbox label="Tags" :suggestions="['Laravel','Vue','React']" :max-tags="5" wire:model="tags"/>
```

**`<x-jaren::field>`** — wraps any control with label + hint + error
```blade
<x-jaren::field label="Date" :error="$errors->first('date')" hint="YYYY-MM-DD">
    <input type="date" wire:model="date" class="inp">
</x-jaren::field>
```

---

### Navigation

**`<x-jaren::accordion>`**
```blade
<x-jaren::accordion :multiple="false" flush divided>
    <x-jaren::accordion.item title="Question" :open="true" icon="question-mark-circle">
        Answer text…
    </x-jaren::accordion.item>
</x-jaren::accordion>
```

**`<x-jaren::breadcrumbs>`**
```blade
<x-jaren::breadcrumbs :items="[['label'=>'Home','href'=>'/'],['label'=>'Current']]"
    separator="slash|chevron|dot" home-icon/>
```

**`<x-jaren::dropdown>`**
```blade
<x-jaren::dropdown align="left|right|center" position="bottom|top">
    <x-slot:trigger><x-jaren::button>Menu</x-jaren::button></x-slot:trigger>
    <x-jaren::dropdown.item icon="user" href="/profile" kbd="⌘P">Profile</x-jaren::dropdown.item>
    <x-jaren::dropdown.separator/>
    <x-jaren::dropdown.item variant="danger">Delete</x-jaren::dropdown.item>
</x-jaren::dropdown>
```

**`<x-jaren::tabs>`**
```blade
<x-jaren::tabs default="tab1" variant="line|pill|box" :full="false">
    <x-jaren::tabs.tab name="tab1" icon="home" badge="3">Overview</x-jaren::tabs.tab>
    <x-jaren::tabs.panel name="tab1" class="pt-4">Content…</x-jaren::tabs.panel>
</x-jaren::tabs>
```

**`<x-jaren::pagination>`**
```blade
{{-- With Livewire paginator: --}}
<x-jaren::pagination :paginator="$users"/>

{{-- Manual: --}}
<x-jaren::pagination :current="3" :total="250" :per-page="10" simple size="sm|md"/>
```

---

### Overlay

**`<x-jaren::modal>`**
```blade
<x-jaren::modal name="confirm" max-width="sm|md|lg|xl|2xl" danger :closeable="true">
    <x-slot:title>Delete?</x-slot:title>
    <x-slot:description>This cannot be undone.</x-slot:description>
    Body content…
    <x-slot:footer>
        <x-jaren::button variant="secondary" @click="$dispatch('close-modal','confirm')">Cancel</x-jaren::button>
        <x-jaren::button variant="danger" wire:click="delete">Delete</x-jaren::button>
    </x-slot:footer>
</x-jaren::modal>
```

Open from PHP: `$this->dispatch('open-modal', name: 'confirm')`
Open from JS: `$dispatch('open-modal', 'confirm')`

**`<x-jaren::tooltip>`**
```blade
<x-jaren::tooltip content="Copy to clipboard" position="top|bottom|left|right" :delay="300">
    <x-jaren::button icon="clipboard"/>
</x-jaren::tooltip>
```

**`<x-jaren::popover>`**
```blade
<x-jaren::popover position="bottom" align="start|center|end" width="260px">
    <x-slot:trigger><x-jaren::button>Open</x-jaren::button></x-slot:trigger>
    <x-slot:content>Popover content here…</x-slot:content>
</x-jaren::popover>
```

---

### Feedback

**`<x-jaren::toast>`** — Driven by the `jaren.toast` Livewire component.
```php
// From Livewire:
$this->dispatch('jaren-toast', type: 'success', title: 'Saved!', message: 'All good.', duration: 4000);

// Using HasToast trait:
use jarenui\Concerns\HasToast;
$this->toast()->success('Saved!', 'Changes applied.');
$this->toast()->persistent()->action('Undo', 'undo-delete')->danger('Deleted', 'Row removed.');
```

**`<x-jaren::callout>`**
```blade
<x-jaren::callout type="info|success|warning|danger" title="Heads up" dismissible>
    Body text here.
</x-jaren::callout>
```

**`<x-jaren::progress>`**
```blade
<x-jaren::progress :value="68" :max="100" label="Storage" show-value color="blue|green|red|yellow" size="xs|sm|md|lg"/>
<x-jaren::progress :value="68" circular :radius="24" show-value/>
```

**`<x-jaren::skeleton>`**
```blade
<x-jaren::skeleton variant="text|avatar|card|table|form" :lines="3" :rows="4"/>
```

---

### Display

**`<x-jaren::card>`**
```blade
<x-jaren::card title="Revenue" description="This month" padding="sm|md|lg" hover shadow>
    <x-slot:footer>
        <x-jaren::button size="sm">View all</x-jaren::button>
    </x-slot:footer>
    $42,810
</x-jaren::card>
```

**`<x-jaren::profile>`**
```blade
<x-jaren::profile name="Jane Doe" handle="janedoe" location="Manila, PH"
    bio="Laravel developer." :stats="[['value'=>'142','label'=>'Projects']]"
    :tags="['Laravel','PHP']" verified compact/>
```

**`<x-jaren::timeline>`**
```blade
<x-jaren::timeline>
    <x-jaren::timeline.item title="Deployed" time="2m ago" status="done" icon="check"/>
    <x-jaren::timeline.item title="Running tests" time="Now" status="active">
        24 tests passing…
    </x-jaren::timeline.item>
    <x-jaren::timeline.item title="Notify team" status="pending"/>
</x-jaren::timeline>
```

---

### Full-stack Livewire components

**Table** — extend `jarenui\Livewire\Table`:
```bash
php artisan jaren:make-table UsersTable --model=User
```
```blade
<livewire:users-table/>
```

**Kanban** — extend `jarenui\Livewire\Kanban`:
```bash
php artisan jaren:make-kanban ProjectKanban
```
```blade
<livewire:project-kanban/>
```

---

## Calendar

A full-featured calendar component for date selection. Supports single dates, multiple dates, and date ranges.

### Basic usage

```blade
<x-jaren::calendar />

{{-- With initial value --}}
<x-jaren::calendar value="2026-05-29" />

{{-- Bound to Livewire --}}
<x-jaren::calendar wire:model="date" />
```

### Multiple dates

```blade
<x-jaren::calendar multiple wire:model="dates" />
```

```php
public array $dates = [];
```

### Date range

```blade
<x-jaren::calendar mode="range" wire:model="range" />
```

```php
use JarenUI\DateRange;

public ?DateRange $range;

public function mount(): void
{
    $this->range = new DateRange(now(), now()->addDays(7));
}
```

### Props

| Prop               | Type / Values                               | Default   |
|--------------------|---------------------------------------------|-----------|
| `wire:model`       | Livewire property binding                   | —         |
| `value`            | `Y-m-d` / `Y-m-d,Y-m-d` / `Y-m-d/Y-m-d`  | —         |
| `mode`             | `single` `multiple` `range`                 | `single`  |
| `min`              | `Y-m-d` or `'today'`                        | —         |
| `max`              | `Y-m-d` or `'today'`                        | —         |
| `unavailable`      | comma-separated `Y-m-d` list                | —         |
| `size`             | `xs` `sm` `base` `lg` `xl` `2xl`           | `base`    |
| `months`           | integer                                     | 1 (2 for range) |
| `min-range`        | integer (days)                              | —         |
| `max-range`        | integer (days)                              | —         |
| `start-day`        | `0`–`6` (0 = Sunday)                        | user locale |
| `with-today`       | bool                                        | `false`   |
| `selectable-header`| bool — click month/year to jump             | `false`   |
| `fixed-weeks`      | bool — always show 6 rows                   | `false`   |
| `week-numbers`     | bool                                        | `false`   |
| `open-to`          | `Y-m-d`                                     | —         |
| `force-open-to`    | bool                                        | `false`   |
| `static`           | bool — display only, no interaction         | `false`   |
| `navigation`       | bool — show prev/next buttons               | `true`    |
| `locale`           | BCP-47 string e.g. `fr`, `ja-JP`            | browser   |

### DateRange object

```php
use JarenUI\DateRange;

$range = new DateRange(now()->subDays(6), now());

$range->start();          // Carbon — start date
$range->end();            // Carbon — end date
$range->length();         // int — number of days inclusive
$range->contains($date);  // bool
$range->toArray();        // Carbon[] — one per day
(string) $range;          // '2026-05-22/2026-05-29'

// With Eloquent:
Order::whereBetween('created_at', $range)->get();
```

Persist in the session automatically:

```php
use Livewire\Attributes\Session;

#[Session]
public ?DateRange $range;
```

### Events

The calendar dispatches an `jaren-calendar-change` Alpine event whenever the selection changes:

```js
document.addEventListener('jaren-calendar-change', (e) => {
    console.log(e.detail.value); // 'Y-m-d' | string[] | {start, end}
});
```

---

## Event Calendar

A full-featured Livewire calendar with month, week, and day views for displaying and managing events.

### Quick start

Generate a calendar component:

```bash
php artisan jaren:make-event-calendar MeetingsCalendar --model=Meeting
```

Use in Blade:

```blade
<livewire:jaren.meetings-calendar />
```

### Static events (no database)

```blade
@php
use JarenUI\CalendarEvent;

$events = [
    new CalendarEvent(
        id:    1,
        title: 'Team standup',
        start: '2026-05-30 09:00',
        end:   '2026-05-30 09:30',
        color: 'blue',
        description: 'Daily sync',
    ),
];
@endphp

<livewire:jaren.event-calendar :events="$events" />
```

### Loading from a database

Override `fetchEvents()` in your subclass. It receives the visible date window as two Carbon instances:

```php
class MeetingsCalendar extends \JarenUI\Livewire\EventCalendar
{
    public function fetchEvents(Carbon $from, Carbon $to): array
    {
        return CalendarEvent::fromCollection(
            Meeting::whereBetween('starts_at', [$from, $to])->get(),
            startKey:       'starts_at',
            endKey:         'ends_at',
            titleKey:       'title',
            colorKey:       'category_color',
            descriptionKey: 'notes',
        );
    }
}
```

`fetchEvents()` is called automatically whenever the view or visible period changes.

### CalendarEvent

```php
use JarenUI\CalendarEvent;

// Construct directly
$event = new CalendarEvent(
    id:          1,
    title:       'Sprint planning',
    start:       '2026-05-30 10:00',
    end:         '2026-05-30 12:00',
    color:       'green',        // blue|green|amber|red|purple|teal|pink|coral|gray
    description: 'Plan Q3 sprint backlog',
    url:         'https://notion.so/sprint-doc',
    allDay:      false,
    meta:        ['room' => 'Conf room A'],
);

// Cast from an Eloquent model
$event = CalendarEvent::from($meeting,
    startKey: 'starts_at',
    endKey:   'ends_at',
);

// Cast from a collection
$events = CalendarEvent::fromCollection(
    Meeting::inMonth(2026, 5)->get(),
    startKey: 'starts_at',
    endKey:   'ends_at',
);

// Accessors
$event->date();             // '2026-05-30'
$event->startTime();        // '10:00'
$event->endTime();          // '12:00'
$event->durationMinutes();  // 120
$event->spansMultipleDays();// false
$event->toArray();          // array for wire:model / JSON
```

### Component props

| Prop              | Type / Values                        | Default      |
|-------------------|--------------------------------------|--------------|
| `events`          | `CalendarEvent[]` or plain arrays    | `[]`         |
| `view`            | `month` `week` `day`                 | `month`      |
| `show-toolbar`    | bool                                 | `true`       |
| `show-detail`     | bool — event detail panel            | `true`       |
| `creatable`       | bool — click empty date to create    | `false`      |
| `start-day`       | `0`–`6` (0 = Sunday, 1 = Monday)     | `0`          |
| `locale`          | BCP-47 string e.g. `fr`, `ja-JP`     | `en`         |
| `available-views` | array of view names                  | all three    |
| `day-start-hour`  | integer                              | `7`          |
| `day-end-hour`    | integer                              | `20`         |

### Override in subclass

```php
class MeetingsCalendar extends \JarenUI\Livewire\EventCalendar
{
    public array  $availableViews = ['month', 'week'];  // hide day view
    public bool   $creatable      = true;
    public int    $startDay       = 1;                  // Monday
    public string $view           = 'week';             // default to week view
    public int    $dayStartHour   = 8;
    public int    $dayEndHour     = 18;

    public function fetchEvents(Carbon $from, Carbon $to): array { ... }
}
```

### Events dispatched

| Event                    | Payload                              | When                         |
|--------------------------|--------------------------------------|------------------------------|
| `jaren-event-selected`   | `{event: array}`                     | User clicks an event         |
| `jaren-event-created`    | `{date: 'Y-m-d'}`                    | User clicks empty date (creatable) |
| `jaren-event-moved`      | `{id, date, start, end}`             | Drag-and-drop (frontend)     |
| `jaren-view-changed`     | `{view, year, month}`                | View or period changes       |
| `jaren-date-clicked`     | `{date: 'Y-m-d'}`                    | Any date click               |

Listen in Livewire:

```php
#[On('jaren-event-selected')]
public function onEventSelected(array $event): void
{
    $this->selectedId = $event['id'];
}

#[On('jaren-event-created')]
public function onEventCreated(string $date): void
{
    $this->dispatch('open-modal', name: 'create-event', date: $date);
}
```

### Event colours

| Value    | Appearance         |
|----------|--------------------|
| `blue`   | Blue (default)     |
| `green`  | Green              |
| `amber`  | Amber / gold       |
| `red`    | Red                |
| `purple` | Purple             |
| `teal`   | Teal               |
| `pink`   | Pink               |
| `coral`  | Coral / orange     |
| `gray`   | Neutral gray       |

---

## Customising views

Publish views to override any component:

```bash
php artisan jaren:publish --views
```

Views land in `resources/views/vendor/jarenui/`. Laravel will prefer these over the package defaults.

---

## Upgrading

```bash
composer update leenuxus/jarenui
php artisan jaren:publish --assets --force
```

---

## Contributing

```bash
git clone https://github.com/leenuxus/jarenui
cd livewire
composer install
vendor/bin/pest
```

---

## License

MIT — see [LICENSE.md](LICENSE.md).
