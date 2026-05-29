{{-- heading.blade.php --}}
@props([
    'level'   => 2,        // 1 | 2 | 3 | 4 | 5 | 6
    'size'    => null,     // override visual size: '4xl' | '3xl' | '2xl' | 'xl' | 'lg' | 'md' | 'sm'
    'weight'  => null,     // override font weight
    'color'   => null,     // override color token
    'tracking'=> null,     // override tracking
])

@php
$tag = 'h' . max(1, min(6, (int)$level));

// Visual size map (level → default visual)
$sizeMap = [
    1 => ['size' => 'text-[22px]', 'weight' => 'font-bold',    'tracking' => 'tracking-[-0.03em]'],
    2 => ['size' => 'text-[18px]', 'weight' => 'font-semibold','tracking' => 'tracking-[-0.025em]'],
    3 => ['size' => 'text-[15px]', 'weight' => 'font-semibold','tracking' => 'tracking-[-0.02em]'],
    4 => ['size' => 'text-[13px]', 'weight' => 'font-semibold','tracking' => 'tracking-[-0.01em]'],
    5 => ['size' => 'text-[12px]', 'weight' => 'font-semibold','tracking' => 'tracking-normal'],
    6 => ['size' => 'text-[11px]', 'weight' => 'font-semibold','tracking' => 'tracking-[0.02em] uppercase'],
];

$namedSizes = [
    '4xl' => 'text-4xl', '3xl' => 'text-3xl', '2xl' => 'text-2xl',
    'xl'  => 'text-xl',  'lg'  => 'text-lg',  'md'  => 'text-base', 'sm' => 'text-sm',
];

$defaults = $sizeMap[(int)$level] ?? $sizeMap[2];
$sizeClass    = $size    ? ($namedSizes[$size] ?? $size) : $defaults['size'];
$weightClass  = $weight  ? "font-{$weight}"              : $defaults['weight'];
$trackingClass= $tracking ? "tracking-[{$tracking}]"    : $defaults['tracking'];
$colorClass   = $color   ? "text-[{$color}]"             : 'text-[var(--text)]';

$classes = implode(' ', ['leading-tight', $sizeClass, $weightClass, $trackingClass, $colorClass]);
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</{{ $tag }}>
