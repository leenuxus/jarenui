@props([
    'name'  => null,
    'src'   => null,       // logo image URL
    'href'  => '/',
    'size'  => 'md',       // sm | md | lg
    'dot'   => false,      // show coloured dot icon instead of image
    'badge' => null,       // e.g. "Beta", "v2"
])

@php
$sizes = [
    'sm' => ['logo' => 'w-5 h-5 text-[12px]', 'name' => 'text-[13px]', 'dot' => 'w-[18px] h-[18px] rounded-[4px]'],
    'md' => ['logo' => 'w-6 h-6 text-[14px]', 'name' => 'text-[15px]', 'dot' => 'w-5 h-5 rounded-[5px]'],
    'lg' => ['logo' => 'w-8 h-8 text-[16px]', 'name' => 'text-lg',     'dot' => 'w-7 h-7 rounded-[6px]'],
];
$sz = $sizes[$size] ?? $sizes['md'];
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 font-semibold text-[var(--text)] tracking-tight no-underline hover:opacity-80 transition-opacity focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)] rounded']) }}
    aria-label="{{ $name ?? 'Home' }}"
>
    {{-- Logo image --}}
    @if($src)
        <img src="{{ $src }}" alt="{{ $name ?? 'Logo' }}" class="{{ $sz['logo'] }} object-contain">

    {{-- Dot/icon placeholder --}}
    @elseif($dot || $slot->isEmpty())
        <span
            class="{{ $sz['dot'] }} bg-[var(--accent)] flex items-center justify-center shrink-0"
            aria-hidden="true"
        >
            <svg class="w-[55%] h-[55%]" viewBox="0 0 12 12" fill="white">
                <path d="M1 1h4v4H1zM7 1h4v4H7zM1 7h4v4H1zM7 7h4v4H7z"/>
            </svg>
        </span>

    {{-- Custom slot --}}
    @elseif(!$slot->isEmpty())
        <span class="{{ $sz['logo'] }} flex items-center justify-center shrink-0">
            {{ $slot }}
        </span>
    @endif

    {{-- Name --}}
    @if($name)
        <span class="{{ $sz['name'] }} leading-none">{{ $name }}</span>
    @endif

    {{-- Badge --}}
    @if($badge)
        <span class="text-[10px] font-semibold px-1.5 py-px bg-[var(--accent-bg)] text-[var(--accent-text)] rounded-full leading-tight">
            {{ $badge }}
        </span>
    @endif
</a>
