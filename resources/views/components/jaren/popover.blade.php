{{-- popover.blade.php --}}
@props([
    'position'  => 'bottom',   // top | bottom | left | right
    'align'     => 'start',    // start | center | end
    'width'     => '260px',
    'closeOnClick' => false,
])

@php
$pos = match("{$position}-{$align}") {
    'bottom-start'  => 'top-full mt-1.5 left-0',
    'bottom-center' => 'top-full mt-1.5 left-1/2 -translate-x-1/2',
    'bottom-end'    => 'top-full mt-1.5 right-0',
    'top-start'     => 'bottom-full mb-1.5 left-0',
    'top-center'    => 'bottom-full mb-1.5 left-1/2 -translate-x-1/2',
    'top-end'       => 'bottom-full mb-1.5 right-0',
    'left-start'    => 'right-full mr-1.5 top-0',
    'right-start'   => 'left-full ml-1.5 top-0',
    default         => 'top-full mt-1.5 left-0',
};
@endphp

<div
    class="relative inline-block"
    x-data="{ open: false }"
    x-on:keydown.escape.window="open = false"
    x-on:click.outside="open = false"
    {{ $attributes->only('class') }}
>
    {{-- Trigger --}}
    <div @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="dialog">
        {{ $trigger ?? $slot }}
    </div>

    {{-- Panel --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 translate-y-[-4px]"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-[-4px]"
        @if($closeOnClick) @click="open = false" @endif
        class="absolute z-50 {{ $pos }} bg-[var(--surface)] border border-[var(--border2)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] p-4"
        style="width: {{ $width }}"
        role="dialog"
    >
        {{ $content ?? '' }}
    </div>
</div>
