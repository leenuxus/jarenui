<?php

namespace JarenUI;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * JarenUI CalendarEvent
 *
 * A simple value object representing a single event on the event calendar.
 * Can be constructed manually or cast from an Eloquent model via CalendarEvent::from().
 *
 * Usage:
 *   $event = new CalendarEvent(
 *       id:    1,
 *       title: 'Team standup',
 *       start: '2026-05-30 09:00',
 *       end:   '2026-05-30 09:30',
 *       color: 'blue',
 *   );
 *
 *   // From Eloquent:
 *   CalendarEvent::from($model, titleKey: 'name', startKey: 'starts_at');
 */
class CalendarEvent implements Arrayable
{
    public readonly string $id;
    public readonly Carbon $start;
    public readonly Carbon $end;

    /**
     * @param  int|string       $id        Unique identifier (used for wire:key)
     * @param  string           $title     Event label shown in the calendar
     * @param  mixed            $start     Carbon-parseable start datetime
     * @param  mixed            $end       Carbon-parseable end datetime (defaults to start + 1 hour)
     * @param  string           $color     blue|green|amber|red|purple|teal|pink|coral|gray
     * @param  string|null      $description  Shown in the event detail popover
     * @param  string|null      $url       If set, clicking opens this URL
     * @param  bool             $allDay    True = no time shown, spans full day
     * @param  array            $meta      Any extra data you want to pass through to Alpine/JS
     */
    public function __construct(
        int|string   $id,
        public string $title,
        mixed         $start,
        mixed         $end         = null,
        public string $color       = 'blue',
        public ?string $description = null,
        public ?string $url         = null,
        public bool    $allDay      = false,
        public array   $meta        = [],
    ) {
        $this->id    = (string) $id;
        $this->start = $start instanceof Carbon ? $start : Carbon::parse($start);
        $endParsed   = $end ? ($end instanceof Carbon ? $end : Carbon::parse($end)) : null;
        $this->end   = $endParsed ?? $this->start->copy()->addHour();
    }

    // ── Factory ───────────────────────────────────────────────────────────────

    /**
     * Cast an Eloquent model (or any array/object) to a CalendarEvent.
     *
     * @param  mixed   $model
     * @param  string  $idKey
     * @param  string  $titleKey
     * @param  string  $startKey
     * @param  string  $endKey
     * @param  string  $colorKey
     */
    public static function from(
        mixed  $model,
        string $idKey          = 'id',
        string $titleKey       = 'title',
        string $startKey       = 'start',
        string $endKey         = 'end',
        string $colorKey       = 'color',
        string $descriptionKey = 'description',
        string $urlKey         = 'url',
        string $allDayKey      = 'all_day',
    ): static {
        $get = fn (string $key) => is_array($model) ? ($model[$key] ?? null) : ($model->{$key} ?? null);

        return new static(
            id:          $get($idKey),
            title:       (string) $get($titleKey),
            start:       $get($startKey),
            end:         $get($endKey),
            color:       (string) ($get($colorKey) ?? 'blue'),
            description: $get($descriptionKey),
            url:         $get($urlKey),
            allDay:      (bool)   ($get($allDayKey) ?? false),
        );
    }

    /**
     * Cast a collection of models to CalendarEvent[].
     *
     * Usage:
     *   CalendarEvent::fromCollection(
     *       Meeting::inMonth($year, $month)->get(),
     *       startKey: 'starts_at',
     *       endKey:   'ends_at',
     *   );
     */
    public static function fromCollection(
        iterable $models,
        string $idKey          = 'id',
        string $titleKey       = 'title',
        string $startKey       = 'start',
        string $endKey         = 'end',
        string $colorKey       = 'color',
        string $descriptionKey = 'description',
        string $urlKey         = 'url',
        string $allDayKey      = 'all_day',
    ): array {
        $events = [];
        foreach ($models as $model) {
            $events[] = static::from(
                model:          $model,
                idKey:          $idKey,
                titleKey:       $titleKey,
                startKey:       $startKey,
                endKey:         $endKey,
                colorKey:       $colorKey,
                descriptionKey: $descriptionKey,
                urlKey:         $urlKey,
                allDayKey:      $allDayKey,
            );
        }
        return $events;
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function date(): string
    {
        return $this->start->toDateString();
    }

    public function startTime(): string
    {
        return $this->start->format('H:i');
    }

    public function endTime(): string
    {
        return $this->end->format('H:i');
    }

    public function durationMinutes(): int
    {
        return (int) $this->start->diffInMinutes($this->end);
    }

    public function spansMultipleDays(): bool
    {
        return $this->start->toDateString() !== $this->end->toDateString();
    }

    // ── Serialisation ─────────────────────────────────────────────────────────

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'start'       => $this->start->toDateTimeString(),
            'end'         => $this->end->toDateTimeString(),
            'date'        => $this->date(),
            'startTime'   => $this->startTime(),
            'endTime'     => $this->endTime(),
            'color'       => $this->color,
            'description' => $this->description,
            'url'         => $this->url,
            'allDay'      => $this->allDay,
            'meta'        => $this->meta,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}
