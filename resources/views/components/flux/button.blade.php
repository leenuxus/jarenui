@props([
    'variant'  => 'primary',   // primary | secondary | ghost | danger | success | warning
    'size'     => 'md',        // sm | md | lg
    'icon'     => null,        // Heroicon name (leading)
    'iconEnd'  => null,        // Heroicon name (trailing)
    'loading'  => false,
    'disabled' => false,
    'href'     => null,        // renders as <a> when set
    'type'     => 'button',
])

@php
$base = 'inline-flex items-center justify-center gap-1.5 font-medium rounded-[var(--radius)] border transition-all duration-100 cursor-pointer select-none whitespace-nowrap tracking-tight focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)] focus-visible:ring-offset-2 disabled:opacity-45 disabled:pointer-events-none';

$variants = [
    'primary'   => 'bg-[var(--accent)] text-white border-[var(--accent)] hover:opacity-85',
    'secondary' => 'bg-[var(--surface)] text-[var(--text)] border-[var(--border2)] hover:bg-[var(--bg2)]',
    'ghost'     => 'bg-transparent text-[var(--text2)] border-transparent hover:bg-[var(--bg2)] hover:text-[var(--text)]',
    'danger'    => 'bg-[var(--danger-bg)] text-[var(--danger-text)] border-[var(--danger)] hover:opacity-85',
    'success'   => 'bg-[var(--success-bg)] text-[var(--success-text)] border-[var(--success)] hover:opacity-85',
    'warning'   => 'bg-[var(--warning-bg)] text-[var(--warning-text)] border-[var(--warning)] hover:opacity-85',
];

$sizes = [
    'sm' => 'h-7 px-2.5 text-xs',
    'md' => 'h-8 px-3.5 text-[13px]',
    'lg' => 'h-10 px-5 text-sm',
];

$iconSizes = ['sm' => 'w-3.5 h-3.5', 'md' => 'w-4 h-4', 'lg' => 'w-[18px] h-[18px]'];

$classes = implode(' ', [
    $base,
    $variants[$variant] ?? $variants['primary'],
    $sizes[$size] ?? $sizes['md'],
    $slot->isEmpty() ? ($sizes[$size] === $sizes['sm'] ? 'w-7 px-0' : ($sizes[$size] === $sizes['md'] ? 'w-8 px-0' : 'w-10 px-0')) : '',
]);

$tag = $href ? 'a' : 'button';
$extraAttrs = $href
    ? ['href' => $href]
    : ['type' => $type, 'disabled' => $disabled || $loading];
@endphp

<{{ $tag }}
    {{ $attributes->merge(['class' => $classes]) }}
    @foreach($extraAttrs as $attr => $val)
        @if($val !== false && $val !== null) {{ $attr }}="{{ $val }}" @endif
    @endforeach
    @if($loading) aria-busy="true" aria-label="Loading" @endif
>
    {{-- Loading spinner --}}
    @if($loading)
        <svg class="{{ $iconSizes[$size] }} animate-spin shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
    @elseif($icon)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="{{ $iconSizes[$size] }} shrink-0" aria-hidden="true"/>
    @endif

    {{-- Label --}}
    @unless($slot->isEmpty())
        <span>{{ $slot }}</span>
    @endunless

    {{-- Trailing icon --}}
    @if($iconEnd && !$loading)
        <x-dynamic-component :component="'heroicon-o-'.$iconEnd" class="{{ $iconSizes[$size] }} shrink-0" aria-hidden="true"/>
    @endif
</{{ $tag }}>
