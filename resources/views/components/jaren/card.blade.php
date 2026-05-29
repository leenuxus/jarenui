@props([
    'title'       => null,
    'description' => null,
    'footer'      => null,
    'header'      => null,       // raw header slot (replaces title+description)
    'padding'     => 'md',       // none | sm | md | lg
    'shadow'      => true,
    'hover'       => false,      // hover lift effect
    'href'        => null,       // renders entire card as link
])

@php
$paddings = [
    'none' => '',
    'sm'   => 'p-3',
    'md'   => 'p-4',
    'lg'   => 'p-6',
];
$bodyPad = $paddings[$padding] ?? $paddings['md'];

$cardClass = implode(' ', array_filter([
    'bg-[var(--surface)] border border-[var(--border)] rounded-[var(--radius-xl)] overflow-hidden',
    $shadow ? 'shadow-[var(--shadow)]' : '',
    $hover  ? 'transition-shadow hover:shadow-[var(--shadow-lg)] cursor-pointer' : '',
    $href   ? 'block' : '',
]));
$tag = $href ? 'a' : 'div';
$extraAttrs = $href ? "href=\"{$href}\"" : '';
@endphp

<{{ $tag }} {{ $extraAttrs }} {{ $attributes->merge(['class' => $cardClass]) }}>

    {{-- Optional top image/media slot --}}
    @isset($media)
        <div class="w-full overflow-hidden">{{ $media }}</div>
    @endisset

    {{-- Header --}}
    @if(isset($header))
        <div class="px-4 pt-4 pb-0">{{ $header }}</div>
    @elseif($title || $description)
        <div class="px-4 pt-4 pb-0">
            @if($title)
                <h3 class="text-[14px] font-semibold text-[var(--text)] leading-tight tracking-[-0.01em]">
                    {{ $title }}
                </h3>
            @endif
            @if($description)
                <p class="text-[12px] text-[var(--text3)] mt-0.5 leading-snug">{{ $description }}</p>
            @endif
        </div>
    @endif

    {{-- Body --}}
    @if(!$slot->isEmpty())
        <div class="{{ $bodyPad }} {{ ($title || $description || isset($header)) ? 'pt-3' : '' }}">
            {{ $slot }}
        </div>
    @endif

    {{-- Footer --}}
    @if(isset($footer))
        <div class="px-4 py-3 border-t border-[var(--border)] bg-[var(--bg2)]">
            {{ $footer }}
        </div>
    @endif

</{{ $tag }}>
