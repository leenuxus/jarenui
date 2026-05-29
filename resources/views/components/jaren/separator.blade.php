@props([
    'orientation' => 'horizontal',   // horizontal | vertical
    'label'       => null,           // optional center label
    'spacing'     => 'md',           // sm | md | lg
])

@php
$spacings = ['sm' => 'my-2', 'md' => 'my-4', 'lg' => 'my-6'];
$vSpacings = ['sm' => 'mx-1', 'md' => 'mx-2', 'lg' => 'mx-3'];

if ($orientation === 'vertical') {
    $classes = implode(' ', [
        'inline-block w-px self-stretch bg-[var(--border)]',
        $vSpacings[$spacing] ?? $vSpacings['md'],
    ]);
} elseif ($label) {
    $classes = implode(' ', [
        'flex items-center gap-3 text-[11px] text-[var(--text3)]',
        $spacings[$spacing] ?? $spacings['md'],
    ]);
} else {
    $classes = implode(' ', [
        'w-full border-none border-t border-[var(--border)] h-px bg-[var(--border)]',
        $spacings[$spacing] ?? $spacings['md'],
    ]);
}
@endphp

@if($orientation === 'vertical')
    <span
        {{ $attributes->merge(['class' => $classes]) }}
        role="separator"
        aria-orientation="vertical"
    ></span>

@elseif($label)
    <div
        {{ $attributes->merge(['class' => $classes]) }}
        role="separator"
        aria-orientation="horizontal"
    >
        <span class="flex-1 h-px bg-[var(--border)]" aria-hidden="true"></span>
        <span>{{ $label }}</span>
        <span class="flex-1 h-px bg-[var(--border)]" aria-hidden="true"></span>
    </div>

@else
    <hr
        {{ $attributes->merge(['class' => $classes]) }}
        role="separator"
        aria-orientation="horizontal"
    >
@endif
