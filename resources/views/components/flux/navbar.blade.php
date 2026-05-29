{{-- navbar.blade.php --}}
{{--
  Usage:
    <x-jaren::navbar>
        <x-slot:brand>
            <x-jaren::brand name="FluxUI"/>
        </x-slot:brand>

        <x-jaren::navbar.item href="/" :active="request()->is('/')">Home</x-jaren::navbar.item>
        <x-jaren::navbar.item href="/docs">Docs</x-jaren::navbar.item>

        <x-slot:end>
            <x-jaren::button size="sm">Get started</x-jaren::button>
        </x-slot:end>
    </x-jaren::navbar>
--}}

@props([
    'sticky'      => false,
    'transparent' => false,
    'bordered'    => true,
])

<nav
    {{ $attributes->merge(['class' => implode(' ', array_filter([
        'flex items-center h-[var(--header-h)] px-4 gap-2',
        $transparent ? 'bg-transparent' : 'bg-[var(--surface)]',
        $bordered ? 'border-b border-[var(--border)]' : '',
        $sticky ? 'sticky top-0 z-40 backdrop-blur-sm' : '',
    ]))]) }}
    role="navigation"
    aria-label="Main navigation"
>
    {{-- Brand slot --}}
    @isset($brand)
        <div class="flex items-center mr-3 pr-4 border-r border-[var(--border)] shrink-0">
            {{ $brand }}
        </div>
    @endisset

    {{-- Nav items --}}
    <div class="flex items-center gap-0.5 flex-1">
        {{ $slot }}
    </div>

    {{-- Right slot --}}
    @isset($end)
        <div class="flex items-center gap-2 ml-auto">
            {{ $end }}
        </div>
    @endisset
</nav>
