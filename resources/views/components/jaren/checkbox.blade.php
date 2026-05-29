@props([
    'label'          => null,
    'description'    => null,
    'error'          => null,
    'indeterminate'  => false,
    'size'           => 'md',    // sm | md | lg
    'id'             => null,
    'card'           => false,   // wrap in a card-style border
])

@php
$checkId = $id ?? 'checkbox-' . \Illuminate\Support\Str::random(6);

$sizes = [
    'sm' => ['box' => 'w-3.5 h-3.5 rounded', 'text' => 'text-xs',     'desc' => 'text-[11px]'],
    'md' => ['box' => 'w-4 h-4 rounded',      'text' => 'text-[13px]', 'desc' => 'text-[11px]'],
    'lg' => ['box' => 'w-5 h-5 rounded-[5px]','text' => 'text-sm',     'desc' => 'text-xs'],
];
$sz = $sizes[$size] ?? $sizes['md'];
@endphp

<div
    {{ $attributes->only('class','wire:key')->merge(['class' => $card ? 'rounded-[var(--radius)] border border-[var(--border)] bg-[var(--surface)] p-3 transition-colors has-[:checked]:border-[var(--accent)] has-[:checked]:bg-[var(--accent-bg)]' : '']) }}
    x-data="{ indeterminate: {{ $indeterminate ? 'true' : 'false' }} }"
>
    <label
        for="{{ $checkId }}"
        class="flex items-start gap-2.5 cursor-pointer select-none group"
    >
        {{-- Checkbox input (visually hidden, custom UI shown via peer classes) --}}
        <div class="relative flex items-center justify-center mt-px shrink-0">
            <input
                type="checkbox"
                id="{{ $checkId }}"
                {{ $attributes->except(['class','wire:key','label','description','error','indeterminate','size','id','card']) }}
                class="peer absolute opacity-0 w-full h-full cursor-pointer m-0"
                :indeterminate="indeterminate"
                aria-describedby="{{ $error ? $checkId.'-err' : ($description ? $checkId.'-desc' : '') }}"
                aria-invalid="{{ $error ? 'true' : 'false' }}"
            >

            {{-- Custom box --}}
            <span
                class="{{ $sz['box'] }} flex items-center justify-center border-[1.5px] transition-all duration-100
                    bg-[var(--surface)] border-[var(--border2)]
                    peer-checked:bg-[var(--accent)] peer-checked:border-[var(--accent)]
                    peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--accent)]/25 peer-focus-visible:ring-offset-1
                    peer-disabled:opacity-50 peer-disabled:cursor-not-allowed
                    {{ $error ? 'border-[var(--danger)]' : '' }}"
                aria-hidden="true"
            >
                {{-- Checkmark (shown when checked) --}}
                <svg
                    class="w-2.5 h-2.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity"
                    viewBox="0 0 10 8" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"
                    x-show="!indeterminate"
                >
                    <path d="M1 4l3 3 5-5"/>
                </svg>

                {{-- Indeterminate dash --}}
                <svg
                    class="w-2.5 h-2.5 text-white"
                    viewBox="0 0 10 2" fill="currentColor"
                    x-show="indeterminate"
                    x-cloak
                >
                    <rect x="0" y="0" width="10" height="2" rx="1"/>
                </svg>
            </span>
        </div>

        {{-- Label + description --}}
        @if($label || $description)
            <div class="flex flex-col gap-0.5 min-w-0">
                @if($label)
                    <span class="{{ $sz['text'] }} font-medium text-[var(--text)] leading-tight group-has-[:disabled]:opacity-50">
                        {{ $label }}
                    </span>
                @endif
                @if($description)
                    <span id="{{ $checkId }}-desc" class="{{ $sz['desc'] }} text-[var(--text3)] leading-snug">
                        {{ $description }}
                    </span>
                @endif
            </div>
        @elseif(!$slot->isEmpty())
            <div class="flex flex-col gap-0.5 min-w-0">{{ $slot }}</div>
        @endif
    </label>

    @if($error)
        <p id="{{ $checkId }}-err" class="mt-1 text-[11px] text-[var(--danger-text)]" role="alert">
            {{ $error }}
        </p>
    @endif
</div>
