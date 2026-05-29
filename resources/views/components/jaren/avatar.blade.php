@props([
    'src'     => null,      // image URL
    'name'    => null,      // used for initials + alt text
    'size'    => 'md',      // xs | sm | md | lg | xl
    'color'   => 'blue',    // blue | purple | coral | teal | pink | green | auto
    'status'  => null,      // online | away | busy | offline
    'shape'   => 'circle',  // circle | square
    'stack'   => false,     // true when inside <x-jaren::avatar-group>
])

@php
// Derive initials from name
$initials = '';
if ($name) {
    $parts = explode(' ', trim($name));
    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}

// If color='auto', derive from name charcode
if ($color === 'auto' && $name) {
    $palette = ['blue','purple','coral','teal','pink','green'];
    $color = $palette[ord($name[0]) % count($palette)];
}

$sizes = [
    'xs' => ['wrap' => 'w-6 h-6',  'text' => 'text-[10px]', 'dot' => 'w-2 h-2 border'],
    'sm' => ['wrap' => 'w-8 h-8',  'text' => 'text-xs',     'dot' => 'w-2.5 h-2.5 border-[1.5px]'],
    'md' => ['wrap' => 'w-10 h-10','text' => 'text-sm',     'dot' => 'w-3 h-3 border-2'],
    'lg' => ['wrap' => 'w-13 h-13','text' => 'text-base',   'dot' => 'w-3.5 h-3.5 border-2'],
    'xl' => ['wrap' => 'w-16 h-16','text' => 'text-lg',     'dot' => 'w-4 h-4 border-2'],
];

$gradients = [
    'blue'   => 'from-blue-400 to-blue-600',
    'purple' => 'from-violet-400 to-violet-600',
    'coral'  => 'from-orange-400 to-orange-600',
    'teal'   => 'from-teal-400 to-teal-600',
    'pink'   => 'from-pink-400 to-pink-600',
    'green'  => 'from-emerald-400 to-emerald-600',
];

$statusColors = [
    'online'  => 'bg-green-500',
    'away'    => 'bg-yellow-400',
    'busy'    => 'bg-red-500',
    'offline' => 'bg-[var(--text3)]',
];

$sz    = $sizes[$size] ?? $sizes['md'];
$shape = $shape === 'square' ? 'rounded-[var(--radius-lg)]' : 'rounded-full';
$stackClass = $stack ? 'ring-2 ring-[var(--surface)] -ml-2 first:ml-0' : '';

$wrapClass = implode(' ', [
    'relative inline-flex items-center justify-center shrink-0 font-semibold text-white select-none overflow-hidden',
    $sz['wrap'], $shape, $stackClass,
    $src ? 'bg-[var(--bg2)]' : 'bg-gradient-to-br ' . ($gradients[$color] ?? $gradients['blue']),
]);
@endphp

<span {{ $attributes->merge(['class' => $wrapClass]) }} aria-label="{{ $name ?? 'Avatar' }}" role="img">
    @if($src)
        <img
            src="{{ $src }}"
            alt="{{ $name ?? '' }}"
            class="w-full h-full object-cover {{ $shape }}"
            loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >
        <span class="{{ $sz['text'] }} font-semibold hidden items-center justify-center w-full h-full">
            {{ $initials ?: '?' }}
        </span>
    @elseif($slot->isNotEmpty())
        {{ $slot }}
    @else
        <span class="{{ $sz['text'] }} font-semibold leading-none" aria-hidden="true">
            {{ $initials ?: '?' }}
        </span>
    @endif

    {{-- Status dot --}}
    @if($status)
        <span
            class="absolute bottom-0 right-0 {{ $sz['dot'] }} rounded-full border-[var(--surface)] {{ $statusColors[$status] ?? 'bg-gray-400' }}"
            aria-label="{{ $status }}"
        ></span>
    @endif
</span>
