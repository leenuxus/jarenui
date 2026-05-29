@props([
    'label'       => null,
    'description' => null,
    'error'       => null,
    'size'        => 'md',    // sm | md | lg
    'id'          => null,
    'align'       => 'left',  // left | right  (label position)
    'card'        => false,
])

@php
$switchId = $id ?? 'switch-' . \Illuminate\Support\Str::random(6);

$sizes = [
    'sm' => ['track' => 'w-8 h-[18px]',   'thumb' => 'w-3 h-3 translate-x-0.5',    'on' => 'translate-x-[18px]', 'text' => 'text-xs',     'desc' => 'text-[11px]'],
    'md' => ['track' => 'w-9 h-5',         'thumb' => 'w-3.5 h-3.5 translate-x-0.5','on' => 'translate-x-[18px]','text' => 'text-[13px]', 'desc' => 'text-[11px]'],
    'lg' => ['track' => 'w-11 h-6',        'thumb' => 'w-4.5 h-4.5 translate-x-0.5','on' => 'translate-x-[22px]','text' => 'text-sm',     'desc' => 'text-xs'],
];
$sz = $sizes[$size] ?? $sizes['md'];

$cardClass = $card
    ? 'rounded-[var(--radius)] border border-[var(--border)] bg-[var(--surface)] p-3 hover:border-[var(--border2)] transition-colors'
    : '';

$labelOrder = $align === 'right' ? 'flex-row-reverse' : 'flex-row';
@endphp

<div
    {{ $attributes->only('class','wire:key')->merge(['class' => $cardClass]) }}
>
    <label
        for="{{ $switchId }}"
        class="flex items-center justify-between gap-3 cursor-pointer select-none group {{ $labelOrder }}"
    >
        {{-- Label + description --}}
        @if($label || $description || !$slot->isEmpty())
            <div class="flex flex-col gap-0.5 flex-1 min-w-0">
                @if($label)
                    <span class="{{ $sz['text'] }} font-medium text-[var(--text)] leading-tight group-has-[:disabled]:opacity-50">
                        {{ $label }}
                    </span>
                @endif
                @if($description)
                    <span class="{{ $sz['desc'] }} text-[var(--text3)] leading-snug">
                        {{ $description }}
                    </span>
                @endif
                @if(!$slot->isEmpty())
                    <div>{{ $slot }}</div>
                @endif
            </div>
        @endif

        {{-- Track + thumb --}}
        <div class="relative inline-flex items-center shrink-0">
            <input
                type="checkbox"
                id="{{ $switchId }}"
                role="switch"
                {{ $attributes->except(['class','wire:key','label','description','error','size','id','align','card']) }}
                class="peer absolute opacity-0 w-full h-full cursor-pointer m-0"
                aria-describedby="{{ $error ? $switchId.'-err' : '' }}"
                aria-invalid="{{ $error ? 'true' : 'false' }}"
            >

            {{-- Track --}}
            <span
                class="{{ $sz['track'] }} relative flex items-center rounded-full border border-transparent transition-colors duration-200
                    bg-[var(--border2)]
                    peer-checked:bg-[var(--accent)]
                    peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--accent)]/30 peer-focus-visible:ring-offset-2 peer-focus-visible:ring-offset-[var(--surface)]
                    peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"
                aria-hidden="true"
            >
                {{-- Thumb --}}
                <span
                    class="{{ $sz['thumb'] }} absolute block rounded-full bg-white shadow-[0_1px_3px_rgba(0,0,0,.2)] transition-transform duration-200
                        peer-checked:{{ $sz['on'] }}"
                ></span>
            </span>
        </div>
    </label>

    @if($error)
        <p id="{{ $switchId }}-err" class="mt-1 text-[11px] text-[var(--danger-text)]" role="alert">
            {{ $error }}
        </p>
    @endif
</div>
