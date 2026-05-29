{{--
  Usage:
    <x-jaren::tabs default="overview" variant="line">
        <x-jaren::tabs.tab name="overview" icon="home">Overview</x-jaren::tabs.tab>
        <x-jaren::tabs.tab name="analytics" icon="chart-bar" badge="3">Analytics</x-jaren::tabs.tab>
        <x-jaren::tabs.tab name="settings">Settings</x-jaren::tabs.tab>

        <x-jaren::tabs.panel name="overview">
            Overview content here…
        </x-jaren::tabs.panel>
        <x-jaren::tabs.panel name="analytics">
            Analytics content here…
        </x-jaren::tabs.panel>
        <x-jaren::tabs.panel name="settings">
            Settings content here…
        </x-jaren::tabs.panel>
    </x-jaren::tabs>
--}}

@props([
    'default' => null,      // name of the initially active tab
    'variant' => 'line',    // line | pill | box
    'size'    => 'md',      // sm | md | lg
    'full'    => false,     // stretch tabs to fill width
])

@php
$tabListClasses = match($variant) {
    'pill' => 'flex gap-1 bg-[var(--bg2)] p-1 rounded-[var(--radius-lg)] w-fit',
    'box'  => 'flex border border-[var(--border)] rounded-[var(--radius-lg)] overflow-hidden w-fit',
    default => 'flex border-b border-[var(--border)]',
};

if ($full) {
    $tabListClasses .= ' w-full';
}
@endphp

<div
    x-data="jarenTabs('{{ $default }}')"
    {{ $attributes->only('class','wire:key') }}
>
    {{-- Tab list --}}
    <div class="{{ $tabListClasses }}" role="tablist" aria-label="Tabs">
        {{ $slot }}
    </div>
</div>
