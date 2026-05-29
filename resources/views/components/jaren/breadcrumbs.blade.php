{{--
  Usage:
    <x-jaren::breadcrumbs :items="[
        ['label' => 'Home',       'href' => '/'],
        ['label' => 'Settings',   'href' => '/settings'],
        ['label' => 'Appearance'],   ← current (no href)
    ]"/>

    Or with slot:
    <x-jaren::breadcrumbs>
        <x-jaren::breadcrumbs.item href="/">Home</x-jaren::breadcrumbs.item>
        <x-jaren::breadcrumbs.item href="/settings">Settings</x-jaren::breadcrumbs.item>
        <x-jaren::breadcrumbs.item>Appearance</x-jaren::breadcrumbs.item>
    </x-jaren::breadcrumbs>
--}}

@props([
    'items'     => [],
    'separator' => 'slash',   // slash | chevron | dot
    'size'      => 'md',      // sm | md | lg
    'homeIcon'  => false,     // replace first item text with a home icon
])

@php
$sizes = ['sm' => 'text-[11px]', 'md' => 'text-[13px]', 'lg' => 'text-sm'];
$sizeClass = $sizes[$size] ?? $sizes['md'];

$sep = match($separator) {
    'chevron' => '<svg class="w-3 h-3 text-[var(--text3)]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m7 4 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    'dot'     => '<span class="w-1 h-1 rounded-full bg-[var(--text3)]" aria-hidden="true"></span>',
    default   => '<span class="text-[var(--text3)]" aria-hidden="true">/</span>',
};
@endphp

<nav aria-label="Breadcrumb" {{ $attributes->only('class') }}>
    <ol class="flex items-center flex-wrap gap-1.5 {{ $sizeClass }}" role="list">
        @if(!empty($items))
            @foreach($items as $i => $item)
                <li class="flex items-center gap-1.5">
                    @if($i > 0)
                        {!! $sep !!}
                    @endif

                    @if(isset($item['href']) && $i < count($items) - 1)
                        <a
                            href="{{ $item['href'] }}"
                            class="text-[var(--text2)] hover:text-[var(--text)] transition-colors focus-visible:outline-none focus-visible:underline"
                        >
                            @if($homeIcon && $i === 0)
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor" aria-label="{{ $item['label'] }}">
                                    <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                {{ $item['label'] }}
                            @endif
                        </a>
                    @else
                        <span
                            class="font-medium text-[var(--text)] {{ $i === count($items) - 1 ? '' : 'text-[var(--text2)]' }}"
                            @if($i === count($items) - 1) aria-current="page" @endif
                        >
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach

        @else
            {{-- Slot-based usage --}}
            {{ $slot }}
        @endif
    </ol>
</nav>
