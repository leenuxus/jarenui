@props([
    'href'     => null,
    'icon'     => null,
    'badge'    => null,
    'active'   => null,
    'disabled' => false,
    'children' => false,   // has nested children (expandable)
])

@php
if ($active === null && $href) {
    $active = request()->is(ltrim(parse_url($href, PHP_URL_PATH) ?? '', '/'))
           || request()->url() === url($href);
}
$tag = $href ? 'a' : 'button';
@endphp

<div
    @if($children) x-data="{ childOpen: {{ $active ? 'true' : 'false' }} }" @endif
>
    <{{ $tag }}
        @if($href) href="{{ $href }}" @else type="button" @endif
        @if($disabled) disabled aria-disabled="true" @endif
        @if($active) aria-current="page" @endif
        @if($children) @click="childOpen = !childOpen" @endif
        {{ $attributes->merge(['class' => implode(' ', array_filter([
            'flex items-center gap-2.5 mx-2 my-0.5 rounded-[var(--radius)] transition-all duration-100',
            'text-[13px] font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)]',
            $disabled ? 'opacity-40 cursor-not-allowed pointer-events-none' : 'cursor-pointer',
            $active
                ? 'bg-[var(--accent-bg)] text-[var(--accent-text)]'
                : 'text-[var(--text2)] hover:bg-[var(--bg2)] hover:text-[var(--text)]',
        ])]) }}
        :class="collapsed ? 'justify-center w-9 h-9 mx-auto' : 'px-2.5 py-1.5 w-[calc(100%-16px)]'"
        :title="collapsed ? '{{ $slot->toHtml() }}' : ''"
    >
        {{-- Icon --}}
        @if($icon)
            <x-dynamic-component
                :component="'heroicon-o-'.$icon"
                class="w-[18px] h-[18px] shrink-0 {{ $active ? 'opacity-100' : 'opacity-70' }}"
                aria-hidden="true"
            />
        @else
            <span class="w-[18px] h-[18px] shrink-0 flex items-center justify-center" aria-hidden="true">
                <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-[var(--accent-text)]' : 'bg-[var(--text3)]' }}"></span>
            </span>
        @endif

        {{-- Label (hidden when collapsed) --}}
        <span
            class="flex-1 truncate leading-none overflow-hidden transition-all"
            x-show="!collapsed"
            x-cloak
        >{{ $slot }}</span>

        {{-- Badge (hidden when collapsed) --}}
        @if($badge)
            <span
                x-show="!collapsed"
                x-cloak
                class="ml-auto shrink-0 text-[10px] font-bold px-1.5 py-px rounded-full leading-none
                    {{ $active ? 'bg-[var(--accent)] text-white' : 'bg-[var(--bg3)] text-[var(--text3)]' }}"
            >{{ $badge }}</span>
        @endif

        {{-- Expand chevron for items with children --}}
        @if($children)
            <svg
                x-show="!collapsed"
                x-cloak
                class="w-3.5 h-3.5 shrink-0 text-[var(--text3)] transition-transform"
                :class="childOpen ? 'rotate-180' : ''"
                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
            >
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z" clip-rule="evenodd"/>
            </svg>
        @endif
    </{{ $tag }}>
</div>
