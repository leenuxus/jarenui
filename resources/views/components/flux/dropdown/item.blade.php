{{-- dropdown/item.blade.php --}}
@props([
    'icon'    => null,
    'iconEnd' => null,
    'href'    => null,
    'variant' => 'default',   // default | danger
    'kbd'     => null,        // keyboard shortcut label
    'active'  => false,
    'disabled'=> false,
])

@php
$tag = $href ? 'a' : 'button';

$base = 'w-full flex items-center gap-2.5 px-3 py-2 text-[13px] transition-colors duration-75 text-left cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[var(--accent)]';

$variantClass = match($variant) {
    'danger' => 'text-[var(--danger-text)] hover:bg-[var(--danger-bg)]',
    default  => implode(' ', [
        'text-[var(--text)]',
        $active ? 'bg-[var(--accent-bg)] text-[var(--accent-text)]' : 'hover:bg-[var(--bg2)]',
    ]),
};

$disabledClass = $disabled ? 'opacity-40 pointer-events-none' : '';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @else type="button" @endif
    @if($disabled) disabled aria-disabled="true" @endif
    role="menuitem"
    {{ $attributes->merge(['class' => "$base $variantClass $disabledClass"]) }}
>
    @if($icon)
        <x-dynamic-component
            :component="'heroicon-o-'.$icon"
            class="w-4 h-4 shrink-0 {{ $variant === 'danger' ? 'text-[var(--danger-text)]' : 'text-[var(--text3)]' }}"
            aria-hidden="true"
        />
    @endif

    <span class="flex-1 truncate">{{ $slot }}</span>

    @if($kbd)
        <kbd class="ml-auto text-[10px] text-[var(--text3)] bg-[var(--bg2)] border border-[var(--border)] rounded px-1 py-px font-mono shrink-0">
            {{ $kbd }}
        </kbd>
    @endif

    @if($iconEnd)
        <x-dynamic-component
            :component="'heroicon-o-'.$iconEnd"
            class="w-4 h-4 shrink-0 text-[var(--text3)]"
            aria-hidden="true"
        />
    @endif
</{{ $tag }}>
