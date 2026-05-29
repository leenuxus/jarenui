@props([
    'label'       => null,
    'hint'        => null,
    'error'       => null,
    'rows'        => 4,
    'resize'      => 'vertical',   // none | vertical | horizontal | both
    'autoResize'  => false,        // auto-grow with content
    'maxLength'   => null,
    'showCount'   => false,        // character counter
    'id'          => null,
])

@php
$textareaId = $id ?? 'textarea-' . \Illuminate\Support\Str::random(6);

$resizeClass = match($resize) {
    'none'       => 'resize-none',
    'horizontal' => 'resize-x',
    'both'       => 'resize',
    default      => 'resize-y',
};

$baseClass = implode(' ', [
    'w-full px-3 py-2 text-[13px] leading-relaxed',
    'bg-[var(--surface)] text-[var(--text)] placeholder-[var(--text3)]',
    'border rounded-[var(--radius)] outline-none transition-all duration-150',
    'focus:ring-2 focus:ring-[var(--accent)]/20 focus:border-[var(--accent)]',
    'disabled:opacity-50 disabled:cursor-not-allowed',
    $resizeClass,
    $error ? 'border-[var(--danger)] focus:border-[var(--danger)] focus:ring-[var(--danger)]/15'
           : 'border-[var(--border2)]',
]);
@endphp

<div
    {{ $attributes->only('class','wire:key')->merge(['class' => 'flex flex-col gap-1']) }}
    @if($autoResize || $showCount) x-data="jarenTextarea()" @endif
>
    @if($label)
        <label for="{{ $textareaId }}" class="text-xs font-medium text-[var(--text2)] leading-none">
            {{ $label }}
            @if($attributes->has('required'))
                <span class="text-[var(--danger-text)] ml-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <textarea
        id="{{ $textareaId }}"
        rows="{{ $rows }}"
        @if($maxLength) maxlength="{{ $maxLength }}" @endif
        @if($autoResize) x-ref="ta" @input="autoGrow($el)" @endif
        @if($showCount) x-ref="ta" @input="count = $el.value.length" @endif
        {{ $attributes->except(['class','wire:key','label','hint','error','rows','resize','auto-resize','max-length','show-count','id'])->merge(['class' => $baseClass]) }}
        aria-describedby="{{ $error ? $textareaId.'-err' : ($hint ? $textareaId.'-hint' : '') }}"
        aria-invalid="{{ $error ? 'true' : 'false' }}"
    >{{ $slot }}</textarea>

    <div class="flex items-start justify-between gap-2">
        <div class="flex-1">
            @if($error)
                <p id="{{ $textareaId }}-err" class="text-[11px] text-[var(--danger-text)]" role="alert">
                    {{ $error }}
                </p>
            @elseif($hint)
                <p id="{{ $textareaId }}-hint" class="text-[11px] text-[var(--text3)]">
                    {{ $hint }}
                </p>
            @endif
        </div>

        @if($showCount)
            <p class="text-[11px] text-[var(--text3)] shrink-0 tabular-nums" aria-live="polite">
                <span x-text="count">0</span>@if($maxLength)/<span>{{ $maxLength }}</span>@endif
            </p>
        @endif
    </div>
</div>

@once
<script>
function jarenTextarea() {
    return {
        count: 0,
        autoGrow(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        },
    };
}
</script>
@endonce
