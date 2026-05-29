@props([
    'title'  => '',
    'time'   => null,
    'status' => 'pending',   // done | active | pending | error
    'icon'   => null,
    'color'  => null,        // override dot color (Tailwind bg-* class)
    'last'   => false,
])

@php
$statuses = [
    'done'    => ['dot' => 'border-[var(--success)] bg-[var(--success-bg)]',  'icon' => 'text-[var(--success-text)]',  'label' => 'Completed'],
    'active'  => ['dot' => 'border-[var(--accent)]  bg-[var(--accent-bg)]',   'icon' => 'text-[var(--accent-text)]',   'label' => 'In progress'],
    'error'   => ['dot' => 'border-[var(--danger)]  bg-[var(--danger-bg)]',   'icon' => 'text-[var(--danger-text)]',   'label' => 'Error'],
    'pending' => ['dot' => 'border-[var(--border2)] bg-[var(--surface)]',     'icon' => 'text-[var(--text3)]',         'label' => 'Pending'],
];
$s = $statuses[$status] ?? $statuses['pending'];

$defaultIcons = [
    'done'    => 'check',
    'active'  => 'arrow-path',
    'error'   => 'x-mark',
    'pending' => 'clock',
];
$iconName = $icon ?? $defaultIcons[$status] ?? 'ellipsis-horizontal';
@endphp

<li
    class="flex gap-3 relative"
    wire:key="{{ $title }}"
    {{ $attributes->only('class') }}
>
    {{-- Connector line --}}
    @unless($last)
        <span
            class="absolute left-[15px] top-8 bottom-0 w-px bg-[var(--border)]"
            aria-hidden="true"
        ></span>
    @endunless

    {{-- Dot --}}
    <div
        class="w-8 h-8 rounded-full border-2 flex items-center justify-center shrink-0 z-[1] {{ $s['dot'] }}"
        aria-label="{{ $s['label'] }}: {{ $title }}"
        role="img"
    >
        <x-dynamic-component
            :component="'heroicon-o-'.$iconName"
            class="w-4 h-4 {{ $s['icon'] }} {{ $status === 'active' ? 'animate-spin' : '' }}"
            aria-hidden="true"
        />
    </div>

    {{-- Content --}}
    <div class="flex-1 pb-6 min-w-0">
        <div class="flex items-start justify-between gap-2 flex-wrap">
            <p class="text-[13px] font-medium text-[var(--text)] leading-tight mt-1">
                {{ $title }}
            </p>
            @if($time)
                <time class="text-[11px] text-[var(--text3)] mt-1 shrink-0">{{ $time }}</time>
            @endif
        </div>

        @if(!$slot->isEmpty())
            <div class="mt-1.5 text-[12px] text-[var(--text2)] leading-relaxed">
                {{ $slot }}
            </div>
        @endif
    </div>
</li>
