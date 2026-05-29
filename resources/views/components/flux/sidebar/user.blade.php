@props([
    'name'   => null,
    'email'  => null,
    'src'    => null,
    'role'   => null,
    'status' => 'online',
])

<div
    class="flex items-center gap-2.5 p-2 rounded-[var(--radius-lg)] cursor-pointer transition-colors hover:bg-[var(--bg2)]"
    :class="collapsed ? 'justify-center' : ''"
    role="button"
    tabindex="0"
    aria-label="User menu"
>
    <x-jaren::avatar
        :name="$name"
        :src="$src"
        :status="$status"
        size="sm"
        color="auto"
        class="shrink-0"
    />

    <div class="flex-1 min-w-0 overflow-hidden" x-show="!collapsed" x-cloak>
        <p class="text-[13px] font-medium text-[var(--text)] truncate leading-tight">{{ $name }}</p>
        <p class="text-[11px] text-[var(--text3)] truncate">
            {{ $role ?? $email }}
        </p>
    </div>

    <button
        type="button"
        x-show="!collapsed"
        x-cloak
        class="shrink-0 w-6 h-6 flex items-center justify-center rounded text-[var(--text3)] hover:text-[var(--text)] transition-colors"
        aria-label="User options"
    >
        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M3 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM8.5 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM15.5 8.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z"/>
        </svg>
    </button>
</div>
