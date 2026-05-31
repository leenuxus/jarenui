@props([
    'label'       => null,
    'hint'        => null,
    'error'       => null,
    'icon'        => null,
    'iconEnd'     => null,
    'prefix'      => null,
    'suffix'      => null,
    'size'        => 'md',
    'id'          => null,
    'clearable'   => false,
    'copyable'    => false,
    'toggleable'  => false,
])

@php
$inputId = $id ?? 'input-' . \Illuminate\Support\Str::random(6);

$sizes = [
    'sm' => ['wrap' => 'h-8',      'text' => 'text-xs',     'icon' => 'w-3.5 h-3.5',      'px' => 'px-2.5'],
    'md' => ['wrap' => 'h-[34px]', 'text' => 'text-[13px]', 'icon' => 'w-4 h-4',           'px' => 'px-3'],
    'lg' => ['wrap' => 'h-10',     'text' => 'text-sm',     'icon' => 'w-[18px] h-[18px]', 'px' => 'px-3.5'],
];
$sz = $sizes[$size] ?? $sizes['md'];

$hasLeftAddon  = $icon || $prefix;
$hasRightAddon = $iconEnd || $suffix || $clearable || $copyable || $error;

$inputPaddingL = $hasLeftAddon  ? 'pl-9' : $sz['px'];
$inputPaddingR = $hasRightAddon ? 'pr-9' : $sz['px'];

// x-ref is needed for BOTH clearable, copyable, and toggleable
$needsRef = $clearable || $copyable || $toggleable;

$inputBase = implode(' ', [
    'w-full', $sz['wrap'], $sz['text'], $inputPaddingL, $inputPaddingR,
    'bg-[var(--surface)] text-[var(--text)] placeholder-[var(--text3)]',
    'border rounded-[var(--radius)] outline-none transition-all duration-150',
    'focus:ring-2 focus:ring-[var(--accent)]/20 focus:border-[var(--accent)]',
    'disabled:opacity-50 disabled:cursor-not-allowed',
    'read-only:bg-[var(--bg2)] read-only:cursor-default',
    $error
        ? 'border-[var(--danger)] focus:border-[var(--danger)] focus:ring-[var(--danger)]/15'
        : 'border-[var(--border2)]',
]);
@endphp

<div
    {{ $attributes->only('class', 'wire:key', 'wire:ignore')->merge(['class' => 'flex flex-col gap-1']) }}
    x-data="jarenInput()"
>
    @if($label)
        <label for="{{ $inputId }}" class="text-xs font-medium text-[var(--text2)] leading-none">
            {{ $label }}
            @if($attributes->has('required'))
                <span class="text-[var(--danger-text)] ml-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative flex items-center">

        @if($icon)
            <span class="absolute left-2.5 pointer-events-none text-[var(--text3)]" aria-hidden="true">
                <x-dynamic-component :component="'heroicon-o-'.$icon" class="{{ $sz['icon'] }}"/>
            </span>
        @elseif($prefix)
            <span class="absolute left-3 text-[13px] text-[var(--text3)] pointer-events-none select-none">
                {{ $prefix }}
            </span>
        @endif
        <input
            id="{{ $inputId }}"
            {{ $attributes->except(['class','wire:key','wire:ignore','label','hint','error','icon','icon-end','prefix','suffix','clearable','copyable','toggleable','size','id'])->merge(['class' => $inputBase]) }}
            @if($needsRef) x-ref="input" @endif
            @if($toggleable) :type="show ? 'text' : 'password'" @endif
            @if($clearable) @input="value = $event.target.value" @endif
            aria-describedby="{{ $hint || $error ? $inputId.'-desc' : '' }}"
            aria-invalid="{{ $error ? 'true' : 'false' }}"
        >

        @if($error)
            <span class="absolute right-2.5 pointer-events-none text-[var(--danger-text)]" aria-hidden="true">
                <svg class="{{ $sz['icon'] }}" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
            </span>
        @elseif($clearable)
            {{-- FIX 2: Clear button now dispatches a native 'input' event so
                 wire:model (and wire:model.live) pick up the empty value --}}
            <button
                type="button"
                x-show="value !== ''"
                x-transition
                @click="
                    value = '';
                    $refs.input.value = '';
                    $refs.input.dispatchEvent(new Event('input', { bubbles: true }));
                    $refs.input.dispatchEvent(new Event('change', { bubbles: true }));
                    $refs.input.focus();
                "
                class="absolute right-2.5 text-[var(--text3)] hover:text-[var(--text)] transition-colors"
                aria-label="Clear input"
            >
                <svg class="{{ $sz['icon'] }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                </svg>
            </button>
        @elseif($copyable)
            {{-- FIX 3: $refs.input now exists because $needsRef includes copyable --}}
            <button
                type="button"
                @click="
                    navigator.clipboard.writeText($refs.input.value);
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                "
                class="absolute right-2.5 text-[var(--text3)] hover:text-[var(--accent-text)] transition-colors"
                :aria-label="copied ? 'Copied!' : 'Copy to clipboard'"
            >
                <template x-if="!copied">
                    <svg class="{{ $sz['icon'] }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M7 3.5A1.5 1.5 0 018.5 2h3.879a1.5 1.5 0 011.06.44l3.122 3.12A1.5 1.5 0 0117 6.622V12.5a1.5 1.5 0 01-1.5 1.5h-1v-3.379a3 3 0 00-.879-2.121L10.5 5.379A3 3 0 008.379 4.5H7v-1z"/>
                        <path d="M4.5 6A1.5 1.5 0 003 7.5v9A1.5 1.5 0 004.5 18h7a1.5 1.5 0 001.5-1.5v-5.879a1.5 1.5 0 00-.44-1.06L9.44 6.439A1.5 1.5 0 008.378 6H4.5z"/>
                    </svg>
                </template>
                <template x-if="copied">
                    <svg class="{{ $sz['icon'] }} text-[var(--success-text)]" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/>
                    </svg>
                </template>
            </button>
        @elseif($toggleable)
            <button
                type="button"
                @click="show = !show; $refs.input.focus()"
                :aria-label="show ? 'Hide password' : 'Show password'"
                class="absolute right-2.5 text-[var(--text3)] hover:text-[var(--text)] transition-colors"
            >
                <template x-if="!show">
                    {{-- Eye --}}
                    <svg class="{{ $sz['icon'] }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/>
                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <template x-if="show">
                    {{-- Eye-slash --}}
                    <svg class="{{ $sz['icon'] }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd"/>
                        <path d="M10.748 13.93l2.523 2.523a10.003 10.003 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z"/>
                    </svg>
                </template>
            </button>
        @elseif($iconEnd)
            <span class="absolute right-2.5 pointer-events-none text-[var(--text3)]" aria-hidden="true">
                <x-dynamic-component :component="'heroicon-o-'.$iconEnd" class="{{ $sz['icon'] }}"/>
            </span>
        @elseif($suffix)
            <span class="absolute right-3 text-[13px] text-[var(--text3)] pointer-events-none select-none">
                {{ $suffix }}
            </span>
        @endif
    </div>

    @if($error)
        <p id="{{ $inputId }}-desc" class="text-[11px] text-[var(--danger-text)] leading-tight" role="alert">
            {{ $error }}
        </p>
    @elseif($hint)
        <p id="{{ $inputId }}-desc" class="text-[11px] text-[var(--text3)] leading-tight">
            {{ $hint }}
        </p>
    @endif
</div>

@once
<script>
function jarenInput() {
    return {
        value: '',
        copied: false,
        show: false,
    };
}
</script>
@endonce