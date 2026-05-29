@props([
    'href'     => null,
    'active'   => null,     // null = auto-detect from request, true/false = manual
    'icon'     => null,
    'badge'    => null,
    'external' => false,
    'disabled' => false,
])

@php
// Auto-detect active if not explicitly set
if ($active === null && $href) {
    $active = request()->is(ltrim(parse_url($href, PHP_URL_PATH) ?? '', '/'))
           || request()->url() === url($href);
}

$tag = $href ? 'a' : 'button';
$classes = implode(' ', array_filter([
    'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[var(--radius)] text-[13px] font-medium',
    'transition-all duration-100 whitespace-nowrap select-none',
    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)]',
    $disabled ? 'opacity-40 pointer-events-none' : '',
    $active
        ? 'bg-[var(--accent-bg)] text-[var(--accent-text)]'
        : 'text-[var(--text2)] hover:bg-[var(--bg2)] hover:text-[var(--text)]',
]));
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @else type="button" @endif
    @if($external) target="_blank" rel="noopener noreferrer" @endif
    @if($disabled) disabled aria-disabled="true" @endif
    @if($active) aria-current="page" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>
    @if($icon)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-4 h-4 shrink-0" aria-hidden="true"/>
    @endif

    {{ $slot }}

    @if($badge)
        <span class="ml-1 text-[10px] font-bold px-1.5 py-px rounded-full
            {{ $active ? 'bg-[var(--accent)] text-white' : 'bg-[var(--bg2)] text-[var(--text2)]' }}">
            {{ $badge }}
        </span>
    @endif

    @if($external)
        <svg class="w-3 h-3 opacity-50" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5z" clip-rule="evenodd"/>
            <path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.194a.75.75 0 00-.053 1.06z" clip-rule="evenodd"/>
        </svg>
    @endif
</{{ $tag }}>
