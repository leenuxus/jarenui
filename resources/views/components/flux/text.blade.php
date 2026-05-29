{{-- text.blade.php --}}
@props([
    'size'    => 'sm',     // xs | sm | md | lg
    'color'   => 'muted',  // default | muted | faint | accent | danger | success | warning
    'weight'  => null,
    'tag'     => 'p',
    'mono'    => false,
    'truncate'=> false,
])

@php
$sizes = [
    'xs' => 'text-[11px] leading-[1.5]',
    'sm' => 'text-[13px] leading-relaxed',
    'md' => 'text-sm     leading-relaxed',
    'lg' => 'text-base   leading-relaxed',
];

$colors = [
    'default' => 'text-[var(--text)]',
    'muted'   => 'text-[var(--text2)]',
    'faint'   => 'text-[var(--text3)]',
    'accent'  => 'text-[var(--accent-text)]',
    'danger'  => 'text-[var(--danger-text)]',
    'success' => 'text-[var(--success-text)]',
    'warning' => 'text-[var(--warning-text)]',
];

$classes = implode(' ', array_filter([
    $sizes[$size]    ?? $sizes['sm'],
    $colors[$color]  ?? $colors['muted'],
    $weight ? "font-{$weight}" : '',
    $mono    ? 'font-mono text-[12px]' : '',
    $truncate ? 'truncate' : '',
]));
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</{{ $tag }}>
