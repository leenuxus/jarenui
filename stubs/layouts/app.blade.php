<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-theme="light"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- jarenUI design tokens --}}
    @jarenStyles

    {{-- App styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Livewire --}}
    @livewireStyles

    @stack('head')
</head>
<body
    class="bg-[var(--bg3)] antialiased"
    x-data="{ sidebarOpen: window.innerWidth >= 1024, dark: document.documentElement.getAttribute('data-theme') === 'dark' }"
    x-cloak
>

<div class="min-h-screen flex flex-col">

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <x-jaren::header sticky>
        <x-slot:brand>
            <x-jaren::brand name="{{ config('app.name') }}" dot/>
        </x-slot:brand>

        <x-slot:nav>
            <x-jaren::navbar.item href="{{ url('/') }}">Home</x-jaren::navbar.item>
            {{-- Add more nav items here --}}
        </x-slot:nav>

        <x-slot:actions>
            {{-- Dark mode toggle --}}
            <button
                type="button"
                @click="
                    dark = !dark;
                    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
                "
                class="w-8 h-8 flex items-center justify-center rounded-[var(--radius)] border border-[var(--border)] text-[var(--text3)] hover:bg-[var(--bg2)] transition-colors"
                :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
            >
                <svg x-show="dark" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/></svg>
                <svg x-show="!dark" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 01.26.77 7 7 0 009.958 7.967.75.75 0 011.067.853A8.5 8.5 0 116.647 1.921a.75.75 0 01.808.083z" clip-rule="evenodd"/></svg>
            </button>

            @auth
                {{-- User avatar + dropdown --}}
                <x-jaren::dropdown align="right">
                    <x-slot:trigger>
                        <x-jaren::avatar
                            :name="auth()->user()->name"
                            size="sm"
                            color="auto"
                            class="cursor-pointer"
                        />
                    </x-slot:trigger>

                    <div class="px-3 py-2.5 border-b border-[var(--border)]">
                        <p class="text-[13px] font-semibold text-[var(--text)]">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-[var(--text3)]">{{ auth()->user()->email }}</p>
                    </div>

                    <x-jaren::dropdown.item icon="cog-6-tooth" href="{{ route('profile.edit') }}">
                        Settings
                    </x-jaren::dropdown.item>
                    <x-jaren::dropdown.separator/>
                    <x-jaren::dropdown.item variant="danger">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left text-[var(--danger-text)]">
                                Sign out
                            </button>
                        </form>
                    </x-jaren::dropdown.item>
                </x-jaren::dropdown>
            @else
                <x-jaren::button href="{{ route('login') }}" variant="secondary" size="sm">
                    Sign in
                </x-jaren::button>
                <x-jaren::button href="{{ route('register') }}" size="sm">
                    Get started
                </x-jaren::button>
            @endauth

            {{-- Mobile sidebar toggle --}}
            <button
                type="button"
                @click="sidebarOpen = !sidebarOpen"
                class="lg:hidden w-8 h-8 flex items-center justify-center rounded-[var(--radius)] border border-[var(--border)] text-[var(--text3)] hover:bg-[var(--bg2)]"
            >
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z" clip-rule="evenodd"/></svg>
            </button>
        </x-slot:actions>
    </x-jaren::header>

    {{-- ── Body ────────────────────────────────────────────────────────── --}}
    <div class="flex flex-1 overflow-hidden" style="height: calc(100vh - var(--header-h))">

        {{-- Mobile overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-black/40 lg:hidden"
        ></div>

        {{-- Sidebar --}}
        <div
            x-show="sidebarOpen"
            class="fixed lg:relative z-40 lg:z-auto h-full lg:flex lg:translate-x-0"
            x-transition:enter="transition ease-in-out duration-200 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
        >
            <x-jaren::sidebar>
                <x-slot:header>
                    <x-jaren::brand name="{{ config('app.name') }}" dot size="sm"/>
                </x-slot:header>

                <x-jaren::sidebar.section label="Menu">
                    <x-jaren::sidebar.item href="{{ url('/') }}" icon="home">
                        Home
                    </x-jaren::sidebar.item>
                    {{-- Add sidebar items here --}}
                </x-jaren::sidebar.section>

                @auth
                    <x-slot:footer>
                        <x-jaren::sidebar.user
                            :name="auth()->user()->name"
                            :email="auth()->user()->email"
                        />
                    </x-slot:footer>
                @endauth
            </x-jaren::sidebar>
        </div>

        {{-- Main content --}}
        <main class="flex-1 overflow-y-auto overflow-x-hidden bg-[var(--bg3)]">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mx-6 mt-4">
                    <x-jaren::callout type="success" :title="session('success')" dismissible/>
                </div>
            @endif
            @if(session('error'))
                <div class="mx-6 mt-4">
                    <x-jaren::callout type="danger" :title="session('error')" dismissible/>
                </div>
            @endif

            <div class="p-6">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

{{-- Toast notifications --}}
<livewire:jaren.toast/>

@livewireScripts
@stack('scripts')
</body>
</html>
