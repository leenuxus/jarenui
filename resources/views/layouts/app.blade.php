{{--
  JarenUI — Complete Application Layout
  resources/views/layouts/app.blade.php

  This is the canonical layout showing how every JarenUI component
  fits together in a real Laravel + Livewire application.
--}}
<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-theme="{{ auth()->user()?->dark_mode ? 'dark' : 'light' }}"
    class="{{ auth()->user()?->theme ? 'theme-'.auth()->user()->theme : '' }}"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    {{-- JarenUI design tokens (CSS variables + base resets) --}}
    @jarenStyles

    {{-- App styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Livewire styles --}}
    @livewireStyles

    @stack('head')
</head>

<body class="bg-[var(--bg3)] antialiased" x-cloak>

{{-- ── App shell ──────────────────────────────────────────────────────────── --}}
<div
    class="min-h-screen flex flex-col"
    x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        darkMode: document.documentElement.getAttribute('data-theme') === 'dark',
        toggleDark() {
            this.darkMode = !this.darkMode;
            document.documentElement.setAttribute('data-theme', this.darkMode ? 'dark' : 'light');
            // Persist (Livewire / fetch)
            fetch('/preferences', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: JSON.stringify({ dark_mode: this.darkMode }),
            });
        },
    }"
>

    {{-- ── Header ───────────────────────────────────────────────────────── --}}
    <x-jaren::header sticky>
        <x-slot:brand>
            <x-jaren::brand name="{{ config('app.name') }}" dot/>
        </x-slot:brand>

        <x-slot:nav>
            <x-jaren::navbar.item href="{{ route('dashboard') }}" icon="squares-2x2">
                Dashboard
            </x-jaren::navbar.item>
            <x-jaren::navbar.item href="{{ route('projects.index') }}" icon="folder" badge="5">
                Projects
            </x-jaren::navbar.item>
            <x-jaren::navbar.item href="{{ route('team.index') }}" icon="user-group">
                Team
            </x-jaren::navbar.item>
            <x-jaren::navbar.item href="{{ route('analytics') }}" icon="chart-bar">
                Analytics
            </x-jaren::navbar.item>
        </x-slot:nav>

        <x-slot:actions>
            {{-- Search --}}
            <x-jaren::tooltip content="Search (⌘K)">
                <button
                    type="button"
                    @click="$dispatch('open-command-palette')"
                    class="h-8 px-3 flex items-center gap-2 rounded-[var(--radius)] border border-[var(--border2)] bg-[var(--bg2)] text-[var(--text3)] text-[13px] hover:bg-[var(--bg3)] transition-colors"
                >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/></svg>
                    <span class="hidden sm:block">Search…</span>
                    <kbd class="hidden sm:block text-[10px] bg-[var(--bg3)] border border-[var(--border)] rounded px-1 py-px font-mono">⌘K</kbd>
                </button>
            </x-jaren::tooltip>

            {{-- Dark mode toggle --}}
            <x-jaren::tooltip :content="'Toggle dark mode'">
                <button
                    type="button"
                    @click="toggleDark()"
                    class="w-8 h-8 flex items-center justify-center rounded-[var(--radius)] border border-[var(--border)] text-[var(--text3)] hover:bg-[var(--bg2)] hover:text-[var(--text)] transition-colors"
                    :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <svg x-show="darkMode" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/></svg>
                    <svg x-show="!darkMode" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 01.26.77 7 7 0 009.958 7.967.75.75 0 011.067.853A8.5 8.5 0 116.647 1.921a.75.75 0 01.808.083z" clip-rule="evenodd"/></svg>
                </button>
            </x-jaren::tooltip>

            {{-- Notifications --}}
            <x-jaren::dropdown align="right" width="72">
                <x-slot:trigger>
                    <div class="relative">
                        <button type="button" class="w-8 h-8 flex items-center justify-center rounded-[var(--radius)] border border-[var(--border)] text-[var(--text3)] hover:bg-[var(--bg2)] hover:text-[var(--text)] transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a6 6 0 00-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 00.515 1.076 32.91 32.91 0 003.256.508 3.5 3.5 0 006.972 0 32.903 32.903 0 003.256-.508.75.75 0 00.515-1.076A11.448 11.448 0 0116 8a6 6 0 00-6-6zM8.05 14.943a33.54 33.54 0 003.9 0 2 2 0 01-3.9 0z" clip-rule="evenodd"/></svg>
                        </button>
                        @if(auth()->user()?->unread_notifications_count > 0)
                            <span class="absolute top-0.5 right-0.5 w-2 h-2 rounded-full bg-[var(--danger)] border border-[var(--surface)]" aria-hidden="true"></span>
                        @endif
                    </div>
                </x-slot:trigger>

                <div class="p-3 border-b border-[var(--border)]">
                    <p class="text-[13px] font-semibold text-[var(--text)]">Notifications</p>
                </div>
                <div class="max-h-72 overflow-y-auto py-1">
                    @forelse(auth()->user()?->latestNotifications ?? [] as $notif)
                        <div class="px-3 py-2.5 hover:bg-[var(--bg2)] transition-colors cursor-pointer">
                            <p class="text-[12px] font-medium text-[var(--text)]">{{ $notif->data['title'] ?? '' }}</p>
                            <p class="text-[11px] text-[var(--text3)] mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-center text-[12px] text-[var(--text3)] py-6">No notifications</p>
                    @endforelse
                </div>
                <div class="border-t border-[var(--border)] p-2">
                    <x-jaren::dropdown.item href="{{ route('notifications') }}" icon="arrow-right">
                        View all notifications
                    </x-jaren::dropdown.item>
                </div>
            </x-jaren::dropdown>

            {{-- User menu --}}
            <x-jaren::dropdown align="right">
                <x-slot:trigger>
                    <x-jaren::avatar
                        :name="auth()->user()?->name"
                        :src="auth()->user()?->profile_photo_url"
                        size="sm"
                        color="auto"
                        class="cursor-pointer"
                    />
                </x-slot:trigger>

                <div class="px-3 py-2.5 border-b border-[var(--border)]">
                    <p class="text-[13px] font-semibold text-[var(--text)]">{{ auth()->user()?->name }}</p>
                    <p class="text-[11px] text-[var(--text3)]">{{ auth()->user()?->email }}</p>
                </div>

                <x-jaren::dropdown.group>
                    <x-jaren::dropdown.item icon="user"        href="{{ route('profile') }}">Profile</x-jaren::dropdown.item>
                    <x-jaren::dropdown.item icon="cog-6-tooth" href="{{ route('settings') }}" kbd="⌘,">Settings</x-jaren::dropdown.item>
                    <x-jaren::dropdown.item icon="credit-card" href="{{ route('billing') }}">Billing</x-jaren::dropdown.item>
                </x-jaren::dropdown.group>

                <x-jaren::dropdown.separator/>

                <x-jaren::dropdown.item icon="arrow-right-on-rectangle" variant="danger">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left text-[var(--danger-text)]">Sign out</button>
                    </form>
                </x-jaren::dropdown.item>
            </x-jaren::dropdown>

            {{-- Mobile sidebar toggle --}}
            <button
                type="button"
                @click="sidebarOpen = !sidebarOpen"
                class="lg:hidden w-8 h-8 flex items-center justify-center rounded-[var(--radius)] border border-[var(--border)] text-[var(--text3)] hover:bg-[var(--bg2)]"
                aria-label="Toggle sidebar"
            >
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z" clip-rule="evenodd"/></svg>
            </button>
        </x-slot:actions>
    </x-jaren::header>

    {{-- ── Body: Sidebar + Main ─────────────────────────────────────────── --}}
    <div class="flex flex-1 overflow-hidden" style="height: calc(100vh - var(--header-h))">

        {{-- Mobile overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-black/40 lg:hidden"
            aria-hidden="true"
        ></div>

        {{-- Sidebar --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition ease-in-out duration-200 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed lg:relative z-40 lg:z-auto h-full lg:translate-x-0 lg:flex flex-col"
        >
            <x-jaren::sidebar :collapsible="true">
                <x-slot:header>
                    <x-jaren::brand name="{{ config('app.name') }}" dot size="sm"/>
                </x-slot:header>

                <x-jaren::sidebar.section label="Menu">
                    <x-jaren::sidebar.item href="{{ route('dashboard') }}"       icon="squares-2x2">Dashboard</x-jaren::sidebar.item>
                    <x-jaren::sidebar.item href="{{ route('projects.index') }}"  icon="folder"      badge="{{ auth()->user()?->projects_count ?? 0 }}">Projects</x-jaren::sidebar.item>
                    <x-jaren::sidebar.item href="{{ route('team.index') }}"      icon="user-group">Team</x-jaren::sidebar.item>
                    <x-jaren::sidebar.item href="{{ route('analytics') }}"       icon="chart-bar">Analytics</x-jaren::sidebar.item>
                    <x-jaren::sidebar.item href="{{ route('inbox') }}"           icon="inbox">Inbox <x-jaren::badge color="red" size="sm">3</x-jaren::badge></x-jaren::sidebar.item>
                </x-jaren::sidebar.section>

                <x-jaren::sidebar.section label="Tools">
                    <x-jaren::sidebar.item href="{{ route('kanban') }}"          icon="view-columns">Kanban</x-jaren::sidebar.item>
                    <x-jaren::sidebar.item href="{{ route('calendar') }}"        icon="calendar">Calendar</x-jaren::sidebar.item>
                    <x-jaren::sidebar.item href="{{ route('files') }}"           icon="folder-open">Files</x-jaren::sidebar.item>
                </x-jaren::sidebar.section>

                <x-jaren::sidebar.section label="Account">
                    <x-jaren::sidebar.item href="{{ route('settings') }}"        icon="cog-6-tooth">Settings</x-jaren::sidebar.item>
                    <x-jaren::sidebar.item href="{{ route('billing') }}"         icon="credit-card">Billing</x-jaren::sidebar.item>
                    <x-jaren::sidebar.item href="https://docs.example.com"       icon="book-open" :external="true">Docs</x-jaren::sidebar.item>
                </x-jaren::sidebar.section>

                <x-slot:footer>
                    <x-jaren::sidebar.user
                        :name="auth()->user()?->name"
                        :email="auth()->user()?->email"
                        :src="auth()->user()?->profile_photo_url"
                        :role="auth()->user()?->role"
                        status="online"
                    />
                </x-slot:footer>
            </x-jaren::sidebar>
        </div>

        {{-- ── Main content ──────────────────────────────────────────────── --}}
        <main class="flex-1 overflow-y-auto overflow-x-hidden">
            {{-- Page heading / breadcrumbs bar --}}
            @hasSection('heading')
                <div class="sticky top-0 z-20 bg-[var(--bg3)]/80 backdrop-blur-sm border-b border-[var(--border)] px-6 py-3 flex items-center justify-between gap-4">
                    <div>
                        @hasSection('breadcrumbs')
                            <x-jaren::breadcrumbs :items="[]">
                                @yield('breadcrumbs')
                            </x-jaren::breadcrumbs>
                        @endif
                        <h1 class="text-[18px] font-semibold text-[var(--text)] tracking-tight mt-0.5">
                            @yield('heading')
                        </h1>
                    </div>
                    @hasSection('actions')
                        <div class="flex items-center gap-2 ml-auto">
                            @yield('actions')
                        </div>
                    @endif
                </div>
            @endif

            {{-- Flash messages --}}
            @if(session()->has('success'))
                <div class="mx-6 mt-4">
                    <x-jaren::callout type="success" :title="session('success')" dismissible/>
                </div>
            @endif
            @if(session()->has('error'))
                <div class="mx-6 mt-4">
                    <x-jaren::callout type="danger" :title="session('error')" dismissible/>
                </div>
            @endif

            {{-- Page content --}}
            <div class="p-6">
                {{ $slot }}
                @yield('content')
            </div>
        </main>
    </div>
</div>

{{-- ── Global overlays ─────────────────────────────────────────────────── --}}

{{-- Toast stack (Livewire component) --}}
<livewire:jaren.toast/>

{{-- Command palette (global ⌘K) --}}
<div
    x-data="{ open: false }"
    @open-command-palette.window="open = true"
    @keydown.meta.k.window.prevent="open = true"
    @keydown.ctrl.k.window.prevent="open = true"
>
    <template x-if="open">
        <div
            class="fixed inset-0 z-[100] flex items-start justify-center pt-[15vh] px-4"
            @click.self="open = false"
            @keydown.escape.window="open = false"
            role="dialog"
            aria-modal="true"
            aria-label="Command palette"
        >
            <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]" @click="open = false" aria-hidden="true"></div>
            <div class="relative w-full max-w-lg bg-[var(--surface)] border border-[var(--border2)] rounded-[var(--radius-xl)] shadow-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-4 py-3 border-b border-[var(--border)]">
                    <svg class="w-4 h-4 text-[var(--text3)]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/></svg>
                    <input
                        type="text"
                        placeholder="Type a command or search…"
                        autofocus
                        class="flex-1 text-[14px] bg-transparent text-[var(--text)] placeholder-[var(--text3)] border-none outline-none"
                    >
                    <kbd class="text-[10px] text-[var(--text3)] bg-[var(--bg2)] border border-[var(--border)] rounded px-1.5 py-0.5 font-mono cursor-pointer" @click="open = false">ESC</kbd>
                </div>
                <div class="pb-2 max-h-72 overflow-y-auto">
                    <p class="px-4 pt-3 pb-1 text-[10px] font-semibold uppercase tracking-widest text-[var(--text3)]">Navigation</p>
                    @foreach([
                        ['href' => route('dashboard'),      'icon' => 'squares-2x2',     'label' => 'Go to Dashboard'],
                        ['href' => route('projects.index'), 'icon' => 'folder',           'label' => 'Go to Projects'],
                        ['href' => route('team.index'),     'icon' => 'user-group',       'label' => 'Go to Team'],
                        ['href' => route('settings'),       'icon' => 'cog-6-tooth',      'label' => 'Open Settings'],
                    ] as $cmd)
                        <a href="{{ $cmd['href'] }}" @click="open = false"
                           class="flex items-center gap-3 px-4 py-2 text-[13px] text-[var(--text)] hover:bg-[var(--bg2)] transition-colors">
                            <x-dynamic-component :component="'heroicon-o-'.$cmd['icon']" class="w-4 h-4 text-[var(--text3)]"/>
                            {{ $cmd['label'] }}
                        </a>
                    @endforeach
                </div>
                <div class="border-t border-[var(--border)] px-4 py-2 flex items-center gap-3 text-[11px] text-[var(--text3)]">
                    <span><kbd class="bg-[var(--bg2)] border border-[var(--border)] rounded px-1 font-mono">↑↓</kbd> navigate</span>
                    <span><kbd class="bg-[var(--bg2)] border border-[var(--border)] rounded px-1 font-mono">↵</kbd> select</span>
                    <span><kbd class="bg-[var(--bg2)] border border-[var(--border)] rounded px-1 font-mono">ESC</kbd> close</span>
                </div>
            </div>
        </div>
    </template>
</div>

{{-- Livewire scripts --}}
@livewireScripts

@stack('scripts')
</body>
</html>
