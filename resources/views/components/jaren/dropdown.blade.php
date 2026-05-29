{{--
  Usage:
    <x-jaren::dropdown>
        <x-slot:trigger>
            <x-jaren::button variant="secondary" icon-end="chevron-down">Account</x-jaren::button>
        </x-slot:trigger>

        <x-jaren::dropdown.item icon="user"        href="/profile">Profile</x-jaren::dropdown.item>
        <x-jaren::dropdown.item icon="cog-6-tooth" href="/settings" kbd="⌘,">Settings</x-jaren::dropdown.item>
        <x-jaren::dropdown.separator/>
        <x-jaren::dropdown.item icon="arrow-right-on-rectangle" variant="danger">Sign out</x-jaren::dropdown.item>
    </x-jaren::dropdown>
--}}

@props([
    'trigger'   => null,
    'align'     => 'left',     // left | right | center
    'position'  => 'bottom',   // bottom | top
    'width'     => '48',       // Tailwind width unit (e.g. 48 → w-48)
    'closeOnSelect' => true,
])

@php
$alignClass = match($align) {
    'right'  => 'right-0 left-auto',
    'center' => 'left-1/2 -translate-x-1/2',
    default  => 'left-0',
};
$positionClass = $position === 'top'
    ? 'bottom-full mb-1.5'
    : 'top-full mt-1.5';
@endphp

<div
    class="relative inline-block text-left"
    x-data="{ open: false }"
    x-on:keydown.escape.window="open = false"
    x-on:click.outside="open = false"
    {{ $attributes->only('class') }}
>
    {{-- Trigger slot --}}
    <div @click="open = !open" aria-haspopup="true" :aria-expanded="open.toString()">
        {{ $trigger }}
    </div>

    {{-- Dropdown panel --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="absolute z-50 {{ $positionClass }} {{ $alignClass }} w-{{ $width }} min-w-[160px] py-1
               bg-[var(--surface)] border border-[var(--border2)] rounded-[var(--radius-lg)]
               shadow-[var(--shadow-lg)] overflow-hidden"
        role="menu"
        aria-orientation="vertical"
    >
        <div
            @if($closeOnSelect)
            @click="open = false"
            @endif
        >
            {{ $slot }}
        </div>
    </div>
</div>
