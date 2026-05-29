{{-- slider.blade.php --}}
@props([
    'label'      => null,
    'min'        => 0,
    'max'        => 100,
    'step'       => 1,
    'showValue'  => true,
    'prefix'     => null,    // e.g. "$"
    'suffix'     => null,    // e.g. "%"
    'id'         => null,
])

@php $sliderId = $id ?? 'slider-' . \Illuminate\Support\Str::random(6); @endphp

<div
    {{ $attributes->only('class','wire:key')->merge(['class' => 'flex flex-col gap-2']) }}
    x-data="{ val: {{ $attributes->get('value', ($min + $max) / 2) }} }"
>
    @if($label || $showValue)
        <div class="flex items-center justify-between">
            @if($label)
                <label for="{{ $sliderId }}" class="text-xs font-medium text-[var(--text2)]">{{ $label }}</label>
            @endif
            @if($showValue)
                <span class="text-xs font-semibold text-[var(--text2)] tabular-nums">
                    {{ $prefix }}<span x-text="val"></span>{{ $suffix }}
                </span>
            @endif
        </div>
    @endif

    <div class="relative flex items-center h-5">
        {{-- Track fill --}}
        <div
            class="absolute h-1 bg-[var(--accent)] rounded-full pointer-events-none"
            :style="`width: ${((val - {{ $min }}) / ({{ $max }} - {{ $min }})) * 100}%`"
        ></div>

        <input
            id="{{ $sliderId }}"
            type="range"
            min="{{ $min }}" max="{{ $max }}" step="{{ $step }}"
            x-model="val"
            {{ $attributes->except(['class','wire:key','label','min','max','step','show-value','prefix','suffix','id','value']) }}
            class="w-full h-1 appearance-none bg-[var(--bg3)] rounded-full cursor-pointer
                [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4
                [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-[var(--accent)]
                [&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-white
                [&::-webkit-slider-thumb]:shadow-md [&::-webkit-slider-thumb]:cursor-pointer
                [&::-webkit-slider-thumb]:transition-transform [&::-webkit-slider-thumb]:hover:scale-110
                [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:rounded-full
                [&::-moz-range-thumb]:bg-[var(--accent)] [&::-moz-range-thumb]:border-2 [&::-moz-range-thumb]:border-white
                focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)]/30 focus-visible:ring-offset-1"
            aria-label="{{ $label ?? 'Range' }}"
            aria-valuemin="{{ $min }}" aria-valuemax="{{ $max }}" :aria-valuenow="val"
        >
    </div>
</div>
