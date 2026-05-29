{{-- callout.blade.php --}}
@props([
    'type'        => 'info',     // info | success | warning | danger
    'title'       => null,
    'icon'        => null,       // override icon (Heroicon name)
    'dismissible' => false,
    'size'        => 'md',       // sm | md
])

@php
$types = [
    'info'    => ['bg' => 'bg-[var(--accent-bg)]',  'border' => 'border-[var(--accent)]',  'title' => 'text-[var(--accent-text)]',  'body' => 'text-[var(--accent-text)]/80',  'icon' => 'information-circle'],
    'success' => ['bg' => 'bg-[var(--success-bg)]', 'border' => 'border-[var(--success)]', 'title' => 'text-[var(--success-text)]', 'body' => 'text-[var(--success-text)]/80', 'icon' => 'check-circle'],
    'warning' => ['bg' => 'bg-[var(--warning-bg)]', 'border' => 'border-[var(--warning)]', 'title' => 'text-[var(--warning-text)]', 'body' => 'text-[var(--warning-text)]/80', 'icon' => 'exclamation-triangle'],
    'danger'  => ['bg' => 'bg-[var(--danger-bg)]',  'border' => 'border-[var(--danger)]',  'title' => 'text-[var(--danger-text)]',  'body' => 'text-[var(--danger-text)]/80',  'icon' => 'x-circle'],
];
$t = $types[$type] ?? $types['info'];
$iconName = $icon ?? $t['icon'];
$pad = $size === 'sm' ? 'px-3 py-2.5' : 'px-4 py-3.5';
@endphp

<div
    {{ $attributes->merge(['class' => "flex gap-3 items-start {$pad} rounded-[var(--radius-lg)] border-l-[3px] {$t['bg']} {$t['border']}"]) }}
    role="alert"
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <x-dynamic-component :component="'heroicon-o-'.$iconName"
        class="w-5 h-5 shrink-0 mt-px {{ $t['title'] }}" aria-hidden="true"/>

    <div class="flex-1 min-w-0">
        @if($title)
            <p class="text-[13px] font-semibold leading-tight {{ $t['title'] }} mb-0.5">{{ $title }}</p>
        @endif
        <div class="text-[12px] leading-relaxed {{ $t['body'] }}">{{ $slot }}</div>
    </div>

    @if($dismissible)
        <button @click="show = false" class="shrink-0 {{ $t['title'] }} opacity-60 hover:opacity-100 transition-opacity" aria-label="Dismiss">
            <svg class="w-4 h-4" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 1l12 12M13 1L1 13"/></svg>
        </button>
    @endif
</div>
