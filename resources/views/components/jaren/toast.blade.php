{{--
  JarenUI Toast Stack — rendered by the Toast Livewire component.
  Place <livewire:jaren.toast /> once in your main layout (before </body>).
--}}

@php
$icons = [
    'success' => '<path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>',
    'danger'  => '<path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"/>',
    'warning' => '<path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>',
    'info'    => '<path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5A.75.75 0 0012 9z" clip-rule="evenodd"/>',
];
$iconColors = [
    'success' => 'text-[var(--success)]',
    'danger'  => 'text-[var(--danger)]',
    'warning' => 'text-[var(--warning)]',
    'info'    => 'text-[var(--accent)]',
];
$barColors = [
    'success' => 'bg-[var(--success)]',
    'danger'  => 'bg-[var(--danger)]',
    'warning' => 'bg-[var(--warning)]',
    'info'    => 'bg-[var(--accent)]',
];
@endphp

{{-- Toast container: fixed bottom-right stack --}}
<div
    class="fixed bottom-4 right-4 z-[9999] flex flex-col gap-2 items-end pointer-events-none"
    aria-live="polite"
    aria-label="Notifications"
    role="status"
>
    @foreach($toasts as $toast)
        <div
            wire:key="toast-{{ $toast['id'] }}"
            x-data="jarenToast({{ $toast['id'] }}, {{ $toast['duration'] }})"
            x-show="visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-4 scale-95"
            @mouseenter="pauseTimer()"
            @mouseleave="resumeTimer()"
            class="pointer-events-auto w-full max-w-xs sm:max-w-sm bg-[var(--surface)] border border-[var(--border)] rounded-[var(--radius-lg)] shadow-[var(--shadow-lg)] overflow-hidden"
            role="alert"
            aria-atomic="true"
        >
            {{-- Main row --}}
            <div class="flex items-start gap-3 p-3.5">
                {{-- Icon --}}
                <svg
                    class="w-5 h-5 shrink-0 mt-px {{ $iconColors[$toast['type']] ?? $iconColors['info'] }}"
                    viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"
                >
                    {!! $icons[$toast['type']] ?? $icons['info'] !!}
                </svg>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    @if($toast['title'])
                        <p class="text-[13px] font-semibold text-[var(--text)] leading-tight">
                            {{ $toast['title'] }}
                        </p>
                    @endif
                    @if($toast['message'])
                        <p class="text-[12px] text-[var(--text2)] mt-0.5 leading-snug">
                            {{ $toast['message'] }}
                        </p>
                    @endif
                    @if($toast['action'])
                        <button
                            type="button"
                            wire:click="handleAction({{ $toast['id'] }}, '{{ $toast['actionEvent'] }}')"
                            class="mt-1.5 text-[12px] font-medium text-[var(--accent-text)] hover:underline focus-visible:outline-none"
                        >
                            {{ $toast['action'] }} →
                        </button>
                    @endif
                </div>

                {{-- Dismiss button --}}
                <button
                    type="button"
                    @click="dismiss()"
                    class="shrink-0 text-[var(--text3)] hover:text-[var(--text)] transition-colors focus-visible:outline-none"
                    aria-label="Dismiss notification"
                >
                    <svg class="w-4 h-4" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M1 1l12 12M13 1L1 13"/>
                    </svg>
                </button>
            </div>

            {{-- Progress bar (when duration > 0) --}}
            @if($toast['duration'] > 0)
                <div class="h-[2px] bg-[var(--bg2)]" aria-hidden="true">
                    <div
                        class="{{ $barColors[$toast['type']] ?? $barColors['info'] }} h-full"
                        x-bind:style="`width: ${progress}%; transition: width ${tickMs}ms linear`"
                    ></div>
                </div>
            @endif
        </div>
    @endforeach
</div>

@once
<script>
function jarenToast(id, duration) {
    return {
        id,
        duration,
        visible: true,
        progress: 100,
        timer: null,
        tickMs: 50,
        elapsed: 0,
        init() {
            if (this.duration > 0) this.startTimer();
        },
        startTimer() {
            this.timer = setInterval(() => {
                this.elapsed += this.tickMs;
                this.progress = Math.max(0, 100 - (this.elapsed / this.duration) * 100);
                if (this.elapsed >= this.duration) this.dismiss();
            }, this.tickMs);
        },
        pauseTimer()  { clearInterval(this.timer); },
        resumeTimer() { if (this.duration > 0) this.startTimer(); },
        dismiss() {
            clearInterval(this.timer);
            this.visible = false;
            // Tell Livewire to remove from array after transition
            setTimeout(() => {
                if (window.Livewire) Livewire.dispatch('jaren-toast-dismissed', { id: this.id });
                this.$wire?.dismiss(this.id);
            }, 250);
        },
    };
}
</script>
@endonce
