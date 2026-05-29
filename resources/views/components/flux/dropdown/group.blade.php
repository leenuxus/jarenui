{{-- dropdown/group.blade.php --}}
@props(['label' => null])

<div role="group" :aria-label="'{{ $label }}'">
    @if($label)
        <div class="px-3 pt-2 pb-1 text-[10px] font-semibold uppercase tracking-widest text-[var(--text3)]" role="presentation">
            {{ $label }}
        </div>
    @endif
    {{ $slot }}
</div>
