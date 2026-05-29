{{-- sidebar/section.blade.php --}}
@props(['label' => null])

<div class="mb-2" role="group" :aria-label="'{{ $label }}'">
    @if($label)
        <div
            class="px-3 pt-3 pb-1 overflow-hidden transition-all"
            x-show="!collapsed"
        >
            <span class="text-[10px] font-semibold uppercase tracking-[.08em] text-[var(--text3)]">
                {{ $label }}
            </span>
        </div>
        <div x-show="collapsed" class="pt-1">
            <div class="mx-2 h-px bg-[var(--border)]"></div>
        </div>
    @endif
    {{ $slot }}
</div>
