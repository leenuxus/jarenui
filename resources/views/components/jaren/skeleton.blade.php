@props([
    'variant' => 'text',     // text | avatar | card | table | form | custom
    'lines'   => 3,          // for text variant
    'rows'    => 4,          // for table variant
    'width'   => null,       // custom width override (Tailwind class e.g. 'w-48')
    'height'  => null,       // custom height (e.g. 'h-12')
    'circle'  => false,      // circular skeleton
    'animate' => true,
])

@php
$shimmer = $animate
    ? 'relative overflow-hidden before:absolute before:inset-0 before:bg-gradient-to-r before:from-transparent before:via-white/10 before:to-transparent before:translate-x-[-100%] before:animate-[shimmer_1.5s_infinite]'
    : '';

$base = "bg-[var(--bg2)] {$shimmer}";
$round = $circle ? 'rounded-full' : 'rounded-[var(--radius)]';
@endphp

@if($variant === 'custom')
    <div {{ $attributes->merge(['class' => "{$base} {$round} {$width} {$height}"]) }}></div>

@elseif($variant === 'avatar')
    <div class="flex items-center gap-3 {{ $attributes->get('class') }}">
        <div class="{{ $base }} rounded-full w-10 h-10 shrink-0"></div>
        <div class="flex flex-col gap-2 flex-1">
            <div class="{{ $base }} rounded h-3 w-2/5"></div>
            <div class="{{ $base }} rounded h-2.5 w-3/5"></div>
        </div>
    </div>

@elseif($variant === 'card')
    <div class="{{ $attributes->merge(['class' => 'bg-[var(--surface)] border border-[var(--border)] rounded-[var(--radius-xl)] overflow-hidden']) }}">
        <div class="{{ $base }} w-full h-28"></div>
        <div class="p-4 flex flex-col gap-2.5">
            <div class="{{ $base }} rounded h-4 w-1/3"></div>
            @for($i = 0; $i < 3; $i++)
                <div class="{{ $base }} rounded h-3 {{ $i === 2 ? 'w-2/3' : 'w-full' }}"></div>
            @endfor
            <div class="flex gap-2 mt-1">
                <div class="{{ $base }} rounded-[var(--radius)] h-8 w-20"></div>
                <div class="{{ $base }} rounded-[var(--radius)] h-8 w-16"></div>
            </div>
        </div>
    </div>

@elseif($variant === 'table')
    <div class="{{ $attributes->merge(['class' => 'border border-[var(--border)] rounded-[var(--radius-lg)] overflow-hidden']) }}">
        <div class="bg-[var(--bg2)] px-4 py-2.5 flex gap-4">
            @for($i = 0; $i < 4; $i++)
                <div class="{{ $base }} rounded h-3 {{ $i === 0 ? 'w-1/4' : ($i === 3 ? 'w-1/6' : 'w-1/3') }}"></div>
            @endfor
        </div>
        @for($row = 0; $row < $rows; $row++)
            <div class="px-4 py-3 flex gap-4 border-t border-[var(--border)]">
                <div class="{{ $base }} rounded-full w-7 h-7 shrink-0"></div>
                @for($col = 0; $col < 3; $col++)
                    <div class="{{ $base }} rounded h-3 flex-1 self-center"></div>
                @endfor
                <div class="{{ $base }} rounded h-5 w-14 self-center"></div>
            </div>
        @endfor
    </div>

@elseif($variant === 'form')
    <div class="{{ $attributes->merge(['class' => 'flex flex-col gap-5']) }}">
        @for($i = 0; $i < $lines; $i++)
            <div class="flex flex-col gap-1.5">
                <div class="{{ $base }} rounded h-3 w-1/4"></div>
                <div class="{{ $base }} rounded-[var(--radius)] h-9 w-full"></div>
            </div>
        @endfor
    </div>

@else
    {{-- text variant --}}
    <div {{ $attributes->merge(['class' => 'flex flex-col gap-2']) }}>
        @for($i = 0; $i < $lines; $i++)
            @php
                $widths = ['w-full','w-11/12','w-4/5','w-3/4','w-2/3','w-1/2'];
                $w = $i === $lines - 1 ? $widths[array_rand(array_slice($widths, 2))] : ($i === 0 ? 'w-full' : $widths[array_rand($widths)]);
            @endphp
            <div class="{{ $base }} rounded h-3 {{ $w }}"></div>
        @endfor
    </div>
@endif

@once
<style>
@keyframes shimmer {
    100% { transform: translateX(100%); }
}
</style>
@endonce
