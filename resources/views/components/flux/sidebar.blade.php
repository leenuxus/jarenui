{{--
  FluxUI Sidebar — full app sidebar layout.

  Usage:
    <x-jaren::sidebar>
        <x-slot:header>
            <x-jaren::brand name="Acme"/>
        </x-slot:header>

        <x-jaren::sidebar.section label="Main">
            <x-jaren::sidebar.item href="/"         icon="home"          :active="request()->is('/')">Dashboard</x-jaren::sidebar.item>
            <x-jaren::sidebar.item href="/projects" icon="folder"        badge="5">Projects</x-jaren::sidebar.item>
            <x-jaren::sidebar.item href="/team"     icon="user-group">Team</x-jaren::sidebar.item>
        </x-jaren::sidebar.section>

        <x-jaren::sidebar.section label="Settings">
            <x-jaren::sidebar.item href="/settings" icon="cog-6-tooth">Settings</x-jaren::sidebar.item>
        </x-jaren::sidebar.section>

        <x-slot:footer>
            <x-jaren::sidebar.user
                :name="auth()->user()->name"
                :email="auth()->user()->email"
            />
        </x-slot:footer>
    </x-jaren::sidebar>
--}}

@props([
    'collapsible' => true,
    'collapsed'   => false,
    'width'       => 'var(--sidebar-w)',
])

<aside
    {{ $attributes->merge(['class' => 'flex flex-col bg-[var(--surface)] border-r border-[var(--border)] h-full overflow-hidden transition-[width] duration-200']) }}
    x-data="{ collapsed: {{ $collapsed ? 'true' : 'false' }} }"
    :style="`width: ${collapsed ? '52px' : '{{ $width }}'}`"
    :aria-label="'Sidebar navigation'"
    role="navigation"
>
    {{-- Header slot --}}
    @isset($header)
        <div
            class="flex items-center h-[var(--header-h)] px-3 border-b border-[var(--border)] shrink-0"
            :class="collapsed ? 'justify-center' : 'gap-2 px-3'"
        >
            <div x-show="!collapsed" class="flex-1 min-w-0">{{ $header }}</div>

            @if($collapsible)
                <button
                    type="button"
                    @click="collapsed = !collapsed"
                    class="w-7 h-7 flex items-center justify-center rounded-[var(--radius)] text-[var(--text3)] hover:bg-[var(--bg2)] hover:text-[var(--text)] transition-colors shrink-0"
                    :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                    :aria-expanded="(!collapsed).toString()"
                >
                    <svg class="w-4 h-4 transition-transform" :class="collapsed ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 010 1.06L8.06 10l3.72 3.72a.75.75 0 11-1.06 1.06l-4.25-4.25a.75.75 0 010-1.06l4.25-4.25a.75.75 0 011.06 0z" clip-rule="evenodd"/>
                    </svg>
                </button>
            @endif
        </div>
    @endisset

    {{-- Scrollable nav area --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden py-2">
        {{ $slot }}
    </div>

    {{-- Footer slot --}}
    @isset($footer)
        <div class="shrink-0 border-t border-[var(--border)] p-2">
            {{ $footer }}
        </div>
    @endisset
</aside>
