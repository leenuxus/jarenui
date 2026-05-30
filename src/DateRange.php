<?php

namespace JarenUI;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use InvalidArgumentException;

/**
 * JarenUI DateRange
 *
 * A CarbonPeriod wrapper for calendar range selections.
 * Implements Livewire's Wireable interface so it can be used
 * directly as a Livewire component property with wire:model.
 *
 * Usage:
 *   public ?DateRange $range;
 *
 *   public function mount(): void
 *   {
 *       $this->range = new DateRange(now()->subDays(7), now());
 *   }
 *
 *   // With Eloquent:
 *   Order::whereBetween('created_at', $this->range)->get();
 *
 *   // With #[Session]:
 *   #[Session] public ?DateRange $range;
 */
class DateRange extends CarbonPeriod
{
    // ── Construction ──────────────────────────────────────────────────────────

    /**
     * Create from two Carbon-parseable values.
     */
    public function __construct(mixed $start, mixed $end)
    {
        $startDate = $start instanceof Carbon ? $start : Carbon::parse($start);
        $endDate   = $end   instanceof Carbon ? $end   : Carbon::parse($end);

        $startDate->startOfDay();
        $endDate->endOfDay();

        parent::__construct(
            $startDate->toDateString(),
            '1 day',
            $endDate->toDateString(),
        );
    }

    /**
     * Create a DateRange from a wire:model string value.
     * Accepts 'Y-m-d/Y-m-d' format emitted by <x-jaren::calendar mode="range">.
     *
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): static
    {
        $parts = explode('/', trim($value));

        if (count($parts) !== 2 || ! $parts[0] || ! $parts[1]) {
            throw new InvalidArgumentException(
                "DateRange::fromString() expects a 'Y-m-d/Y-m-d' formatted string, [{$value}] given."
            );
        }

        return new static($parts[0], $parts[1]);
    }

    /**
     * Create from an array with 'start' and 'end' keys.
     */
    public static function fromArray(array $value): static
    {
        if (empty($value['start']) || empty($value['end'])) {
            throw new InvalidArgumentException(
                "DateRange::fromArray() requires 'start' and 'end' keys."
            );
        }

        return new static($value['start'], $value['end']);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /**
     * Get the start date as a Carbon instance.
     */
    public function start(): Carbon
    {
        return Carbon::parse($this->getStartDate())->startOfDay();
    }

    /**
     * Get the end date as a Carbon instance.
     */
    public function end(): Carbon
    {
        return Carbon::parse($this->getEndDate())->endOfDay();
    }

    /**
     * Get the number of days in the range (inclusive).
     */
    public function length(): int
    {
        return (int) $this->start()->diffInDays($this->end()) + 1;
    }

    /**
     * Check whether a given date falls within this range.
     */
    public function contains(mixed $date): bool
    {
        $d = $date instanceof Carbon ? $date : Carbon::parse($date);
        $d->startOfDay();

        return $d->greaterThanOrEqualTo($this->start())
            && $d->lessThanOrEqualTo($this->end()->startOfDay());
    }

    /**
     * Return an array of Carbon instances for each day in the range.
     *
     * @return Carbon[]
     */
    public function toArray(): array
    {
        return iterator_to_array($this, false);
    }

    // ── Serialisation (Livewire Wireable) ─────────────────────────────────────

    /**
     * Serialise to the wire:model string format 'Y-m-d/Y-m-d'.
     * Used by Livewire when hydrating/dehydrating this property.
     */
    public function toLivewire(): string
    {
        return $this->start()->toDateString() . '/' . $this->end()->toDateString();
    }

    /**
     * Reconstitute from the wire:model string value.
     */
    public static function fromLivewire(mixed $value): static
    {
        if (is_array($value)) {
            return static::fromArray($value);
        }

        return static::fromString((string) $value);
    }

    // ── Stringable ────────────────────────────────────────────────────────────

    public function __toString(): string
    {
        return $this->toLivewire();
    }

    // ── Eloquent helpers ──────────────────────────────────────────────────────

    /**
     * Return a [start, end] array suitable for Eloquent's whereBetween().
     *
     * Usage:
     *   Order::whereBetween('created_at', $range->forWhereBetween())->get();
     *   // or simply:
     *   Order::whereBetween('created_at', $range)->get();  // works via CarbonPeriod
     */
    public function forWhereBetween(): array
    {
        return [$this->start(), $this->end()];
    }
}
