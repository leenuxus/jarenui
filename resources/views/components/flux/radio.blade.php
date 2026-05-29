@props([
    'value'       => null,
    'label'       => null,
    'description' => null,
    'badge'       => null,       // e.g. "Popular", "Recommended"
    'price'       => null,       // shown right-aligned
    'icon'        => null,       // Heroicon name
    'disabled'    => false,
    'size'        => 'md',       // sm | md | lg
    'variant'     => 'default',  // default | card — inherited from parent, but can override
    'id'          => null,
])

@php
$radioId = $id ?? 'radio-' . \Illuminate\Support\Str::random(6);

$sizes = [
    'sm' => ['dot' => 'w-3.5 h-3.5', 'inner' => 'w-1.5 h-1.5', 'text' => 'text-xs',     'desc' => 'text-[10px]'],
    'md' => ['dot' => 'w-4 h-4',     'inner' => 'w-2 h-2',     'text' => 'text-[13px]', 'desc' => 'text-[11px]'],
    'lg' => ['dot' => 'w-5 h-5',     'inner' => 'w-2.5 h-2.5', 'text' => 'text-sm',     'desc' => 'text-xs'],
];
$sz = $sizes[$size] ?? $sizes['md'];

// Card variant wraps the whole item in a bordered box
$isCard = $variant === 'card';
$labelClass = implode(' ', [
    'flex items-start gap-2.5 cursor-pointer select-none group',
    $isCard ? implode(' ', [
        'relative w-full p-3 rounded-[var(--radius-lg)] border transition-all duration-100',
        'border-[var(--border)] bg-[var(--surface)] hover:border-[var(--border2)]',
        'has-[:checked]:border-[var(--accent)] has-[:checked]:bg-[var(--accent-bg)]',
        'has-[:disabled]:opacity-50 has-[:disabled]:cursor-not-allowed',
    ]) : '',
]);
@endphp

<label for="{{ $radioId }}" class="{{ $labelClass }}">
    {{-- Hidden native radio --}}
    <input
        type="radio"
        id="{{ $radioId }}"
        value="{{ $value }}"
        @if($disabled) disabled @endif
        {{ $attributes->except(['class','wire:key','value','label','description','badge','price','icon','disabled','size','variant','id']) }}
        class="peer absolute opacity-0 w-0 h-0"
        aria-describedby="{{ $description ? $radioId.'-desc' : '' }}"
    >

    {{-- Custom radio dot --}}
    <span
        class="{{ $sz['dot'] }} mt-px rounded-full border-[1.5px] flex items-center justify-center shrink-0 transition-all duration-100
            border-[var(--border2)] bg-[var(--surface)]
            peer-checked:border-[var(--accent)]
            peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--accent)]/25 peer-focus-visible:ring-offset-1
            peer-disabled:opacity-50"
        aria-hidden="true"
    >
        <span
            class="{{ $sz['inner'] }} rounded-full bg-[var(--accent)] scale-0 transition-transform duration-150
                peer-checked:scale-100"
        ></span>
    </span>

    {{-- Optional leading icon --}}
    @if($icon)
        <x-dynamic-component
            :component="'heroicon-o-'.$icon"
            class="w-5 h-5 shrink-0 mt-px text-[var(--text3)] group-has-[:checked]:text-[var(--accent-text)]"
            aria-hidden="true"
        />
    @endif

    {{-- Label + description --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            @if($label)
                <span class="{{ $sz['text'] }} font-medium text-[var(--text)] leading-tight
                    group-has-[:checked]:text-[var(--accent-text)]
                    group-has-[:disabled]:opacity-50">
                    {{ $label }}
                </span>
            @endif
            @if($badge)
                <span class="text-[10px] font-semibold px-1.5 py-px bg-[var(--accent-bg)] text-[var(--accent-text)] rounded-full leading-tight">
                    {{ $badge }}
                </span>
            @endif
        </div>

        @if($description)
            <p id="{{ $radioId }}-desc" class="{{ $sz['desc'] }} text-[var(--text3)] mt-0.5 leading-snug
                group-has-[:checked]:text-[var(--accent-text)]/70">
                {{ $description }}
            </p>
        @endif

        @if(!$slot->isEmpty())
            <div class="mt-1">{{ $slot }}</div>
        @endif
    </div>

    {{-- Price (right-aligned) --}}
    @if($price)
        <span class="{{ $sz['text'] }} font-semibold text-[var(--text2)] shrink-0 ml-auto
            group-has-[:checked]:text-[var(--accent-text)]">
            {{ $price }}
        </span>
    @endif

    {{-- Card check indicator (top-right corner) --}}
    @if($isCard)
        <span
            class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full flex items-center justify-center
                opacity-0 scale-75 transition-all duration-150
                group-has-[:checked]:opacity-100 group-has-[:checked]:scale-100
                bg-[var(--accent)]"
            aria-hidden="true"
        >
            <svg class="w-2.5 h-2.5 text-white" viewBox="0 0 10 8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 4l3 3 5-5"/>
            </svg>
        </span>
    @endif
</label>
