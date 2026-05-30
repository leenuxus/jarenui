<?php

namespace JarenUI\Livewire\Casts;

use JarenUI\DateRange;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

/**
 * Livewire Synth (synthesizer) for the DateRange value object.
 *
 * Registered in JarenUIServiceProvider::boot() so Livewire knows how to
 * hydrate/dehydrate DateRange properties automatically.
 *
 * Developers do NOT use this class directly — just type-hint DateRange
 * on a Livewire property and wire:model "just works":
 *
 *   public ?DateRange $range;
 *
 *   // In Blade:
 *   <x-jaren::calendar mode="range" wire:model.live="range"/>
 */
class DateRangeSynth extends Synth
{
    /** Unique key identifying this synthesizer to Livewire's internals. */
    public static string $key = 'jaren_date_range';

    // ── Livewire Synth contract ───────────────────────────────────────────────

    /**
     * Tell Livewire which objects this synth handles.
     */
    public static function match(mixed $target): bool
    {
        return $target instanceof DateRange;
    }

    /**
     * Dehydrate: convert a DateRange → a plain serialisable value for JSON transport.
     *
     * @return array{0: string, 1: array{class: string}}
     */
    public function dehydrate(DateRange $target): array
    {
        return [
            $target->toLivewire(),
            ['class' => DateRange::class],
        ];
    }

    /**
     * Hydrate: reconstruct a DateRange from the transported string value.
     */
    public function hydrate(mixed $value, array $meta): DateRange
    {
        return DateRange::fromLivewire($value);
    }

    /**
     * Handle model updates coming from wire:model on the frontend.
     *
     * The calendar emits 'Y-m-d/Y-m-d' for a completed range
     * or 'Y-m-d' for a partial selection (only start chosen).
     */
    public function set(DateRange &$model, string $key, mixed $value): void
    {
        // Allow null / empty string to clear the selection
        if (! $value) {
            return;
        }

        $model = DateRange::fromLivewire($value);
    }
}
