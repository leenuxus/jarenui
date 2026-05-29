{{--
  Usage:
    <x-jaren::accordion>
        <x-jaren::accordion.item title="What is Livewire?" :open="true">
            Livewire is a full-stack framework for Laravel...
        </x-jaren::accordion.item>
        <x-jaren::accordion.item title="How does theming work?">
            All tokens are CSS variables...
        </x-jaren::accordion.item>
    </x-jaren::accordion>
--}}

@props([
    'multiple' => false,   // allow multiple items open simultaneously
    'flush'    => false,   // remove outer border/radius (flush with parent)
    'divided'  => true,    // show dividers between items
])

<div
    {{ $attributes->merge(['class' => implode(' ', [
        'w-full',
        $flush ? '' : 'border border-[var(--border)] rounded-[var(--radius-lg)] bg-[var(--surface)] overflow-hidden shadow-[var(--shadow)]',
    ])]) }}
    x-data="{ openItems: [], multiple: {{ $multiple ? 'true' : 'false' }} }"
    role="list"
>
    {{ $slot }}
</div>
