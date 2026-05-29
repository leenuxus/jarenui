{{--
  Full-featured app header layout component.

  Usage:
    <x-jaren::header>
        <x-slot:brand>
            <x-jaren::brand name="Acme" :src="asset('logo.svg')"/>
        </x-slot:brand>

        <x-slot:nav>
            <x-jaren::navbar.item href="/">Dashboard</x-jaren::navbar.item>
            <x-jaren::navbar.item href="/projects">Projects</x-jaren::navbar.item>
        </x-slot:nav>

        <x-slot:actions>
            <x-jaren::button variant="ghost" icon="bell" class="relative">
                <span class="absolute top-0.5 right-0.5 w-2 h-2 rounded-full bg-red-500 border border-white"></span>
            </x-jaren::button>
            <x-jaren::avatar name="{{ auth()->user()->name }}" size="sm" status="online"/>
        </x-slot:actions>
    </x-jaren::header>
--}}

@props([
    'sticky'  => true,
    'bordered'=> true,
    'blur'    => true,
])

<header
    {{ $attributes->merge(['class' => implode(' ', array_filter([
        'flex items-center h-[var(--header-h)] px-4 gap-3 z-40',
        'bg-[var(--surface)]',
        $blur    ? 'backdrop-blur-sm bg-[var(--surface)]/90' : '',
        $bordered ? 'border-b border-[var(--border)]' : '',
        $sticky  ? 'sticky top-0' : '',
    ]))]) }}
    role="banner"
>
    {{-- Brand --}}
    @isset($brand)
        <div class="flex items-center shrink-0 min-w-[var(--sidebar-w)] pr-4 border-r border-[var(--border)]">
            {{ $brand }}
        </div>
    @endisset

    {{-- Center nav --}}
    @isset($nav)
        <nav class="flex items-center gap-0.5 flex-1" aria-label="Primary navigation">
            {{ $nav }}
        </nav>
    @else
        <div class="flex-1"></div>
    @endisset

    {{-- Search (optional) --}}
    @isset($search)
        <div class="flex items-center">{{ $search }}</div>
    @endisset

    {{-- Right actions --}}
    @isset($actions)
        <div class="flex items-center gap-2 ml-auto">
            {{ $actions }}
        </div>
    @endisset

    {{-- Default slot as fallback --}}
    @if($slot->isNotEmpty() && !isset($brand) && !isset($nav) && !isset($actions))
        {{ $slot }}
    @endif
</header>
