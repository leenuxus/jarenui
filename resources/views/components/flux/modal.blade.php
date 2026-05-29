{{--
  Usage (from any Blade/Livewire template):

    <x-jaren::modal name="delete-user" max-width="md">
        <x-slot:title>Delete user</x-slot:title>
        <x-slot:description>This action cannot be undone.</x-slot:description>

        <p>Body content here…</p>

        <x-slot:footer>
            <x-jaren::button variant="secondary" @click="$dispatch('close-modal', 'delete-user')">
                Cancel
            </x-jaren::button>
            <x-jaren::button variant="danger" wire:click="deleteUser">
                Delete
            </x-jaren::button>
        </x-slot:footer>
    </x-jaren::modal>

  Open from JS / Alpine:
    $dispatch('open-modal', 'delete-user')

  Open from Livewire PHP:
    $this->dispatch('open-modal', name: 'delete-user');
--}}

@props([
    'name',
    'maxWidth'   => 'md',      // sm | md | lg | xl | 2xl | full
    'closeable'  => true,
    'title'      => null,
    'description'=> null,
    'footer'     => null,
    'danger'     => false,     // red header accent for destructive modals
])

@php
$maxWidths = [
    'sm'   => 'max-w-sm',
    'md'   => 'max-w-md',
    'lg'   => 'max-w-lg',
    'xl'   => 'max-w-xl',
    '2xl'  => 'max-w-2xl',
    'full' => 'max-w-full mx-4',
];
$widthClass = $maxWidths[$maxWidth] ?? $maxWidths['md'];
@endphp

<div
    x-data="jarenModal('{{ $name }}', {{ $closeable ? 'true' : 'false' }})"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
    aria-modal="true"
    role="dialog"
    :aria-label="'{{ $title ?? $name }}'"
    @keydown.escape.window="closeable && close()"
>
    {{-- Backdrop --}}
    <div
        class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity"
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closeable && close()"
        aria-hidden="true"
    ></div>

    {{-- Panel --}}
    <div
        class="relative w-full {{ $widthClass }} bg-[var(--surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] overflow-hidden"
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
    >
        {{-- Danger stripe --}}
        @if($danger)
            <div class="h-1 w-full bg-[var(--danger)]"></div>
        @endif

        {{-- Header --}}
        @if($title || $description || $closeable)
            <div class="flex items-start justify-between gap-4 px-5 pt-5 pb-0">
                <div class="flex flex-col gap-0.5 min-w-0">
                    @if($title)
                        <h2 class="text-[15px] font-semibold text-[var(--text)] leading-tight tracking-tight">
                            {{ is_string($title) ? $title : $title }}
                        </h2>
                    @endif
                    @if($description)
                        <p class="text-[12px] text-[var(--text3)] leading-snug">
                            {{ is_string($description) ? $description : $description }}
                        </p>
                    @endif
                </div>
                @if($closeable)
                    <button
                        type="button"
                        @click="close()"
                        class="shrink-0 w-7 h-7 flex items-center justify-center rounded-[var(--radius)] border border-[var(--border)] text-[var(--text3)] hover:bg-[var(--bg2)] hover:text-[var(--text)] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)]"
                        aria-label="Close modal"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M1 1l12 12M13 1L1 13"/>
                        </svg>
                    </button>
                @endif
            </div>
        @endif

        {{-- Body --}}
        <div class="px-5 py-4 text-[13px] text-[var(--text2)] leading-relaxed">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @if($footer)
            <div class="px-5 py-3 bg-[var(--bg2)] border-t border-[var(--border)] flex items-center justify-end gap-2">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

@once
<script>
function jarenModal(name, closeable = true) {
    return {
        name,
        closeable,
        show: false,
        init() {
            // Listen for open/close events
            window.addEventListener('open-modal',  (e) => { if ((e.detail?.name ?? e.detail) === this.name) this.open();  });
            window.addEventListener('close-modal', (e) => { if ((e.detail?.name ?? e.detail) === this.name) this.close(); });
            // Livewire v3 event bus
            if (window.Livewire) {
                Livewire.on('open-modal',  ({ name }) => { if (name === this.name) this.open();  });
                Livewire.on('close-modal', ({ name }) => { if (name === this.name) this.close(); });
            }
        },
        open()  {
            this.show = true;
            document.body.classList.add('overflow-hidden');
            this.$nextTick(() => this.$el.querySelector('[autofocus]')?.focus());
        },
        close() {
            if (!this.closeable) return;
            this.show = false;
            document.body.classList.remove('overflow-hidden');
        },
    };
}
</script>
@endonce
