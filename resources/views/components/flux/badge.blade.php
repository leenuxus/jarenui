@props([
    'color'  => 'blue',   // blue | green | red | yellow | gray | purple | pink
    'size'   => 'md',     // sm | md | lg
    'dot'    => false,    // show colored dot indicator
    'icon'   => null,     // leading Heroicon name
    'pill'   => true,     // rounded-full vs rounded
    'close'  => false,    // show × dismiss button (emits 'dismiss')
])

@php
$colors = [
    'blue'   => 'bg-[var(--accent-bg)] text-[var(--accent-text)]',
    'green'  => 'bg-[var(--success-bg)] text-[var(--success-text)]',
    'red'    => 'bg-[var(--danger-bg)] text-[var(--danger-text)]',
    'yellow' => 'bg-[var(--warning-bg)] text-[var(--warning-text)]',
    'gray'   => 'bg-[var(--bg2)] text-[var(--text2)] border border-[var(--border)]',
    'purple' => 'bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-300',
    'pink'   => 'bg-pink-50 text-pink-700 dark:bg-pink-950 dark:text-pink-300',
];

$dotColors = [
    'blue' => 'bg-[var(--accent-text)]', 'green' => 'bg-[var(--success-text)]',
    'red'  => 'bg-[var(--danger-text)]', 'yellow' => 'bg-[var(--warning-text)]',
    'gray' => 'bg-[var(--text3)]',       'purple' => 'bg-purple-600',
    'pink' => 'bg-pink-600',
];

$sizes = [
    'sm' => 'text-[10px] px-1.5 py-px leading-4',
    'md' => 'text-[11px] px-2 py-0.5 leading-[18px]',
    'lg' => 'text-xs px-2.5 py-1',
];

$classes = implode(' ', [
    'inline-flex items-center gap-1 font-semibold',
    $pill ? 'rounded-full' : 'rounded-[var(--radius)]',
    $colors[$color] ?? $colors['gray'],
    $sizes[$size] ?? $sizes['md'],
]);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{-- Dot indicator --}}
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$color] ?? 'bg-current' }}" aria-hidden="true"></span>
    @endif

    {{-- Leading icon --}}
    @if($icon)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-3 h-3 shrink-0" aria-hidden="true"/>
    @endif

    {{ $slot }}

    {{-- Dismiss button --}}
    @if($close)
        <button
            type="button"
            wire:click="$dispatch('badge-dismiss')"
            class="ml-0.5 -mr-0.5 opacity-60 hover:opacity-100 transition-opacity focus:outline-none"
            aria-label="Dismiss"
        >
            <svg class="w-3 h-3" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true">
                <path d="M9 3 3 9M3 3l6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </button>
    @endif
</span>
