<?php

namespace JarenUI\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use JarenUI\CalendarEvent;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * JarenUI EventCalendar — full-featured Livewire calendar with month/week/day views.
 *
 * Extend this class to load real events from your database:
 *
 *   class MeetingsCalendar extends \JarenUI\Livewire\EventCalendar
 *   {
 *       public function fetchEvents(Carbon $from, Carbon $to): array
 *       {
 *           return CalendarEvent::fromCollection(
 *               Meeting::whereBetween('starts_at', [$from, $to])->get(),
 *               startKey: 'starts_at',
 *               endKey:   'ends_at',
 *               colorKey: 'category_color',
 *           );
 *       }
 *   }
 *
 * Or use inline via the Blade component with a static event list:
 *
 *   <livewire:jaren.event-calendar :events="$events" />
 *
 * Listen to events dispatched by this component:
 *
 *   Livewire::on('jaren-event-selected',  fn($event)  => ...);
 *   Livewire::on('jaren-event-created',   fn($date)   => ...);
 *   Livewire::on('jaren-event-moved',     fn($id, $date, $start, $end) => ...);
 *   Livewire::on('jaren-view-changed',    fn($view, $year, $month)     => ...);
 *   Livewire::on('jaren-date-clicked',    fn($date)   => ...);
 */
class EventCalendar extends Component
{
    // ── Configuration (override in subclass) ──────────────────────────────────

    /** Which views are available in the toolbar. */
    public array $availableViews = ['month', 'week', 'day'];

    /** Show the toolbar (navigation + view switcher). */
    public bool $showToolbar = true;

    /** Show a detail panel when an event is clicked. */
    public bool $showDetail = true;

    /** Allow clicking an empty day to create a new event (emits jaren-event-created). */
    public bool $creatable = false;

    /** First day of the week: 0 = Sunday, 1 = Monday … 6 = Saturday */
    public int $startDay = 0;

    /** Hour at which the time grid starts (week/day views). */
    public int $dayStartHour = 7;

    /** Hour at which the time grid ends. */
    public int $dayEndHour = 20;

    /** BCP-47 locale string for date/time formatting, or null for browser default. */
    public ?string $locale = null;

    // ── State ─────────────────────────────────────────────────────────────────

    public string $view = 'month';

    public int $year;
    public int $month;

    /** ISO date string (Y-m-d) of the selected day in day view. */
    public ?string $dayDate = null;

    /** ISO date string (Y-m-d) of the first day of the current week view. */
    public ?string $weekStart = null;

    /** Currently selected event id (shown in detail panel). */
    public ?string $selectedEventId = null;

    /** Static event list — used when events are passed as a prop. */
    public array $staticEvents = [];

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(
        array  $events        = [],
        string $view          = 'month',
        int    $startDay      = 0,
        ?string $locale       = null,
        bool   $showToolbar   = true,
        bool   $showDetail    = true,
        bool   $creatable     = false,
    ): void {
        $today = Carbon::today();

        $this->year         = $today->year;
        $this->month        = $today->month;
        $this->dayDate      = $today->toDateString();
        $this->weekStart    = $today->startOfWeek($startDay)->toDateString();
        $this->view         = $view;
        $this->startDay     = $startDay;
        $this->locale       = $locale;
        $this->showToolbar  = $showToolbar;
        $this->showDetail   = $showDetail;
        $this->creatable    = $creatable;

        // Normalise static events passed as props
        $this->staticEvents = array_map(
            fn ($e) => $e instanceof CalendarEvent ? $e->toArray() : (array) $e,
            $events
        );
    }

    // ── Event fetching (override in subclass) ─────────────────────────────────

    /**
     * Return an array of CalendarEvent objects for the given date range.
     * Override this in your subclass to load events from your database.
     *
     * @return CalendarEvent[]
     */
    public function fetchEvents(Carbon $from, Carbon $to): array
    {
        return [];
    }

    // ── Computed properties ───────────────────────────────────────────────────

    #[Computed]
    public function events(): array
    {
        [$from, $to] = $this->currentWindow();
        $dynamic = $this->fetchEvents($from, $to);

        $dynamicArrays = array_map(
            fn ($e) => $e instanceof CalendarEvent ? $e->toArray() : (array) $e,
            $dynamic
        );

        // Merge static + dynamic, static takes lower priority
        $merged = array_merge($this->staticEvents, $dynamicArrays);

        // Filter to window
        $fromStr = $from->toDateString();
        $toStr   = $to->toDateString();

        return array_values(array_filter(
            $merged,
            fn ($e) => isset($e['date']) && $e['date'] >= $fromStr && $e['date'] <= $toStr
        ));
    }

    #[Computed]
    public function selectedEvent(): ?array
    {
        if (! $this->selectedEventId) {
            return null;
        }

        foreach ($this->events as $e) {
            if (($e['id'] ?? null) == $this->selectedEventId) {
                return $e;
            }
        }

        return null;
    }

    #[Computed]
    public function currentTitle(): string
    {
        return match ($this->view) {
            'month' => Carbon::create($this->year, $this->month, 1)
                ->locale($this->locale ?? 'en')
                ->isoFormat('MMMM YYYY'),

            'week' => $this->weekStart
                ? $this->buildWeekTitle()
                : '',

            'day' => $this->dayDate
                ? Carbon::parse($this->dayDate)
                    ->locale($this->locale ?? 'en')
                    ->isoFormat('dddd, MMMM D, YYYY')
                : '',

            default => '',
        };
    }

    #[Computed]
    public function calendarDays(): array
    {
        $firstOfMonth = Carbon::create($this->year, $this->month, 1);
        $lastOfMonth  = $firstOfMonth->copy()->endOfMonth();

        // Pad to fill grid: start from first visible day (respecting startDay)
        $gridStart = $firstOfMonth->copy()->startOfWeek($this->startDay);
        $gridEnd   = $lastOfMonth->copy()->endOfWeek($this->startDay);

        $days  = [];
        $today = Carbon::today()->toDateString();
        $cursor = $gridStart->copy();

        while ($cursor <= $gridEnd) {
            $dateStr   = $cursor->toDateString();
            $dayEvents = array_values(array_filter(
                $this->events,
                fn ($e) => ($e['date'] ?? '') === $dateStr
            ));

            usort($dayEvents, fn ($a, $b) => ($a['startTime'] ?? '') <=> ($b['startTime'] ?? ''));

            $days[] = [
                'date'       => $dateStr,
                'day'        => $cursor->day,
                'isToday'    => $dateStr === $today,
                'isCurrentMonth' => $cursor->month === $this->month,
                'dayOfWeek'  => $cursor->dayOfWeek,
                'events'     => $dayEvents,
                'eventCount' => count($dayEvents),
            ];

            $cursor->addDay();
        }

        return $days;
    }

    #[Computed]
    public function calendarWeeks(): array
    {
        $days  = $this->calendarDays;
        $weeks = array_chunk($days, 7);
        return $weeks;
    }

    #[Computed]
    public function weekDays(): array
    {
        if (! $this->weekStart) {
            return [];
        }

        $start  = Carbon::parse($this->weekStart);
        $today  = Carbon::today()->toDateString();
        $days   = [];

        for ($i = 0; $i < 7; $i++) {
            $date     = $start->copy()->addDays($i);
            $dateStr  = $date->toDateString();
            $dayEvts  = array_values(array_filter(
                $this->events,
                fn ($e) => ($e['date'] ?? '') === $dateStr
            ));

            usort($dayEvts, fn ($a, $b) => ($a['startTime'] ?? '') <=> ($b['startTime'] ?? ''));

            $days[] = [
                'date'      => $dateStr,
                'day'       => $date->day,
                'dayName'   => $date->locale($this->locale ?? 'en')->isoFormat('ddd'),
                'isToday'   => $dateStr === $today,
                'events'    => $dayEvts,
            ];
        }

        return $days;
    }

    #[Computed]
    public function dayEvents(): array
    {
        if (! $this->dayDate) {
            return [];
        }

        $evts = array_values(array_filter(
            $this->events,
            fn ($e) => ($e['date'] ?? '') === $this->dayDate
        ));

        usort($evts, fn ($a, $b) => ($a['startTime'] ?? '') <=> ($b['startTime'] ?? ''));

        return $evts;
    }

    #[Computed]
    public function timeSlots(): array
    {
        $slots = [];
        for ($h = $this->dayStartHour; $h <= $this->dayEndHour; $h++) {
            $slots[] = [
                'hour'  => $h,
                'label' => $h === 12
                    ? '12 pm'
                    : ($h < 12 ? "{$h} am" : ($h - 12) . ' pm'),
            ];
        }
        return $slots;
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function setView(string $view): void
    {
        if (! in_array($view, $this->availableViews)) {
            return;
        }
        $this->view = $view;
        $this->dispatch('jaren-view-changed', view: $view, year: $this->year, month: $this->month);
    }

    public function prevPeriod(): void
    {
        match ($this->view) {
            'month' => $this->shiftMonth(-1),
            'week'  => $this->shiftWeek(-1),
            'day'   => $this->shiftDay(-1),
            default => null,
        };
    }

    public function nextPeriod(): void
    {
        match ($this->view) {
            'month' => $this->shiftMonth(1),
            'week'  => $this->shiftWeek(1),
            'day'   => $this->shiftDay(1),
            default => null,
        };
    }

    public function goToToday(): void
    {
        $today           = Carbon::today();
        $this->year      = $today->year;
        $this->month     = $today->month;
        $this->dayDate   = $today->toDateString();
        $this->weekStart = $today->copy()->startOfWeek($this->startDay)->toDateString();
    }

    public function selectEvent(string $id): void
    {
        $this->selectedEventId = $this->selectedEventId === $id ? null : $id;
        $evt = $this->selectedEvent;
        if ($evt) {
            $this->dispatch('jaren-event-selected', event: $evt);
        }
    }

    public function dismissEvent(): void
    {
        $this->selectedEventId = null;
    }

    public function clickDate(string $date): void
    {
        $this->dispatch('jaren-date-clicked', date: $date);

        if ($this->creatable) {
            $this->dispatch('jaren-event-created', date: $date);
        }
    }

    public function drillDown(string $date): void
    {
        $this->dayDate = $date;
        $this->setView('day');
    }

    /** Called from Alpine drag-and-drop to move an event. */
    #[On('jaren-event-dropped')]
    public function onEventDropped(string $id, string $date, ?string $start = null, ?string $end = null): void
    {
        // Update static events array in memory
        foreach ($this->staticEvents as &$e) {
            if (($e['id'] ?? null) == $id) {
                $e['date']      = $date;
                $e['start']     = $date . ' ' . ($start ?? $e['startTime'] ?? '00:00');
                $e['end']       = $date . ' ' . ($end   ?? $e['endTime']   ?? '01:00');
                $e['startTime'] = $start ?? $e['startTime'];
                $e['endTime']   = $end   ?? $e['endTime'];
                break;
            }
        }
        unset($e);

        $this->dispatch('jaren-event-moved', id: $id, date: $date, start: $start, end: $end);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    protected function shiftMonth(int $dir): void
    {
        $d = Carbon::create($this->year, $this->month, 1)->addMonths($dir);
        $this->year  = $d->year;
        $this->month = $d->month;
    }

    protected function shiftWeek(int $dir): void
    {
        $ws = Carbon::parse($this->weekStart ?? Carbon::today()->startOfWeek($this->startDay)->toDateString());
        $this->weekStart = $ws->addWeeks($dir)->toDateString();
    }

    protected function shiftDay(int $dir): void
    {
        $d = Carbon::parse($this->dayDate ?? Carbon::today()->toDateString());
        $this->dayDate = $d->addDays($dir)->toDateString();
    }

    protected function currentWindow(): array
    {
        return match ($this->view) {
            'week' => [
                Carbon::parse($this->weekStart ?? Carbon::today()->startOfWeek($this->startDay)->toDateString()),
                Carbon::parse($this->weekStart ?? Carbon::today()->startOfWeek($this->startDay)->toDateString())->addDays(6)->endOfDay(),
            ],
            'day' => [
                Carbon::parse($this->dayDate ?? Carbon::today()->toDateString())->startOfDay(),
                Carbon::parse($this->dayDate ?? Carbon::today()->toDateString())->endOfDay(),
            ],
            default => [
                Carbon::create($this->year, $this->month, 1)->startOfWeek($this->startDay),
                Carbon::create($this->year, $this->month, 1)->endOfMonth()->endOfWeek($this->startDay),
            ],
        };
    }

    protected function buildWeekTitle(): string
    {
        $start = Carbon::parse($this->weekStart)->locale($this->locale ?? 'en');
        $end   = $start->copy()->addDays(6);

        return $start->year === $end->year
            ? $start->isoFormat('MMM D') . ' – ' . $end->isoFormat('MMM D, YYYY')
            : $start->isoFormat('MMM D, YYYY') . ' – ' . $end->isoFormat('MMM D, YYYY');
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('jarenui::components.jaren.event-calendar');
    }
}
