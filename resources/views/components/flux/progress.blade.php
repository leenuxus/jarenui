{{-- progress.blade.php --}}
@props([
    'value'    => 0,       // 0–100
    'max'      => 100,
    'label'    => null,
    'showValue'=> false,
    'color'    => 'blue',  // blue | green | red | yellow | purple
    'size'     => 'md',    // xs | sm | md | lg
    'animated' => false,   // striped animation
    'circular' => false,   // circular progress ring
    'radius'   => 20,      // only for circular
])

@php
$pct = $max > 0 ? min(100, max(0, ($value / $max) * 100)) : 0;

$colors = [
    'blue'   => 'bg-[var(--accent)]',
    'green'  => 'bg-[var(--success)]',
    'red'    => 'bg-[var(--danger)]',
    'yellow' => 'bg-[var(--warning)]',
    'purple' => 'bg-purple-500',
];
$barColor = $colors[$color] ?? $colors['blue'];

$heights = ['xs' => 'h-1', 'sm' => 'h-1.5', 'md' => 'h-2', 'lg' => 'h-3'];
$trackH = $heights[$size] ?? $heights['md'];

if ($circular) {
    $r         = (int) $radius;
    $circ      = round(2 * M_PI * $r, 2);
    $dashOffset = round($circ * (1 - $pct / 100), 2);
    $viewSize  = ($r + 6) * 2;
}
@endphp

@if($circular)
    <div {{ $attributes->merge(['class' => 'inline-flex flex-col items-center gap-1']) }}>
        <svg
            width="{{ $viewSize }}" height="{{ $viewSize }}"
            viewBox="0 0 {{ $viewSize }} {{ $viewSize }}"
            role="progressbar"
            aria-valuenow="{{ $value }}"
            aria-valuemin="0"
            aria-valuemax="{{ $max }}"
            aria-label="{{ $label ?? 'Progress: '.$value.'%' }}"
        >
            {{-- Track --}}
            <circle
                cx="{{ $viewSize / 2 }}" cy="{{ $viewSize / 2 }}" r="{{ $r }}"
                fill="none" stroke="var(--bg3)" stroke-width="5"
            />
            {{-- Bar --}}
            <circle
                cx="{{ $viewSize / 2 }}" cy="{{ $viewSize / 2 }}" r="{{ $r }}"
                fill="none" stroke="var(--accent)" stroke-width="5"
                stroke-linecap="round"
                stroke-dasharray="{{ $circ }}"
                stroke-dashoffset="{{ $dashOffset }}"
                transform="rotate(-90 {{ $viewSize / 2 }} {{ $viewSize / 2 }})"
                style="transition: stroke-dashoffset .5s ease"
            />
            {{-- Center text --}}
            @if($showValue)
                <text
                    x="{{ $viewSize / 2 }}" y="{{ $viewSize / 2 + 5 }}"
                    text-anchor="middle"
                    font-size="13" font-weight="700"
                    fill="var(--text)"
                    font-family="DM Sans,system-ui,sans-serif"
                >{{ round($pct) }}%</text>
            @endif
        </svg>
        @if($label)
            <span class="text-[11px] text-[var(--text3)]">{{ $label }}</span>
        @endif
    </div>

@else
    <div {{ $attributes->merge(['class' => 'flex flex-col gap-1.5']) }}>
        @if($label || $showValue)
            <div class="flex items-center justify-between">
                @if($label)
                    <span class="text-[12px] font-medium text-[var(--text2)]">{{ $label }}</span>
                @endif
                @if($showValue)
                    <span class="text-[12px] font-semibold tabular-nums {{ $colors[$color] ? '' : '' }} text-[var(--text2)]">
                        {{ round($pct) }}%
                    </span>
                @endif
            </div>
        @endif

        <div
            class="w-full {{ $trackH }} bg-[var(--bg3)] rounded-full overflow-hidden"
            role="progressbar"
            aria-valuenow="{{ $value }}"
            aria-valuemin="0"
            aria-valuemax="{{ $max }}"
            aria-label="{{ $label ?? 'Progress' }}"
        >
            <div
                class="{{ $barColor }} h-full rounded-full transition-[width] duration-500 ease-out
                    {{ $animated ? 'bg-[length:16px_16px] animate-[progress-stripe_1s_linear_infinite]' : '' }}"
                style="width: {{ $pct }}%"
            ></div>
        </div>
    </div>
@endif
