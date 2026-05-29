{{--
  Usage:
    <x-jaren::tooltip content="Copy to clipboard">
        <x-jaren::button variant="ghost" icon="clipboard"/>
    </x-jaren::tooltip>

    <x-jaren::tooltip position="right">
        <x-slot:content>
            <strong>Pro feature</strong><br>Upgrade to access this.
        </x-slot:content>
        <x-jaren::button variant="primary">Upgrade</x-jaren::button>
    </x-jaren::tooltip>
--}}

@props([
    'content'  => null,      // simple string content (use slot:content for rich HTML)
    'position' => 'top',     // top | bottom | left | right
    'delay'    => 300,       // show delay in ms
    'maxWidth' => '200px',
    'arrow'    => true,
])

@php
// Tooltip panel placement
$placements = [
    'top'    => ['panel' => 'bottom-full mb-2 left-1/2 -translate-x-1/2',  'arrow' => 'top-full left-1/2 -translate-x-1/2 border-t-[var(--text)] border-b-transparent border-x-transparent'],
    'bottom' => ['panel' => 'top-full mt-2 left-1/2 -translate-x-1/2',    'arrow' => 'bottom-full left-1/2 -translate-x-1/2 border-b-[var(--text)] border-t-transparent border-x-transparent'],
    'left'   => ['panel' => 'right-full mr-2 top-1/2 -translate-y-1/2',   'arrow' => 'left-full top-1/2 -translate-y-1/2 border-l-[var(--text)] border-r-transparent border-y-transparent'],
    'right'  => ['panel' => 'left-full ml-2 top-1/2 -translate-y-1/2',    'arrow' => 'right-full top-1/2 -translate-y-1/2 border-r-[var(--text)] border-l-transparent border-y-transparent'],
];
$placement = $placements[$position] ?? $placements['top'];
@endphp

<div
    class="relative inline-flex"
    x-data="{ show: false, timeout: null }"
    @mouseenter="timeout = setTimeout(() => show = true, {{ $delay }})"
    @mouseleave="clearTimeout(timeout); show = false"
    @focusin="show = true"
    @focusout="show = false"
    {{ $attributes->only('class') }}
>
    {{-- Trigger --}}
    {{ $slot }}

    {{-- Tooltip panel --}}
    <div
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-[60] pointer-events-none {{ $placement['panel'] }}"
        role="tooltip"
        style="max-width: {{ $maxWidth }}"
    >
        <div class="relative bg-[var(--text)] text-[var(--bg)] text-[11px] leading-snug px-2.5 py-1.5 rounded-[var(--radius)] shadow-lg whitespace-normal text-center">
            @if($content)
                {{ $content }}
            @elseif(isset($tooltipContent))
                {!! $tooltipContent !!}
            @endif

            @if($arrow)
                <span
                    class="absolute {{ $placement['arrow'] }} w-0 h-0 border-4"
                    aria-hidden="true"
                ></span>
            @endif
        </div>
    </div>
</div>
