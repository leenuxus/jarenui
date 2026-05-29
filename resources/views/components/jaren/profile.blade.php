{{-- profile.blade.php --}}
@props([
    'name'        => null,
    'handle'      => null,
    'location'    => null,
    'bio'         => null,
    'src'         => null,
    'coverGradient' => 'from-blue-500 via-violet-500 to-pink-500',
    'stats'       => [],      // [['value'=>'142','label'=>'Projects'],…]
    'tags'        => [],      // string[]
    'actions'     => true,    // show follow/message buttons
    'compact'     => false,   // minimal horizontal layout
    'verified'    => false,
])

@php
$initials = '';
if ($name) {
    $parts    = explode(' ', trim($name));
    $initials = strtoupper(substr($parts[0],0,1).(isset($parts[1])?substr($parts[1],0,1):''));
}
@endphp

@if($compact)
    {{-- Compact horizontal row --}}
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 p-3 bg-[var(--surface)] border border-[var(--border)] rounded-[var(--radius-lg)]']) }}>
        <x-jaren::avatar :name="$name" :src="$src" size="md" color="auto"/>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1.5">
                <span class="text-[13px] font-semibold text-[var(--text)] truncate">{{ $name }}</span>
                @if($verified)
                    <svg class="w-3.5 h-3.5 text-[var(--accent-text)] shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.403 12.652a3 3 0 000-5.304 3 3 0 00-3.75-3.751 3 3 0 00-5.305 0 3 3 0 00-3.751 3.75 3 3 0 000 5.305 3 3 0 003.75 3.751 3 3 0 005.305 0 3 3 0 003.751-3.75zm-2.546-4.46a.75.75 0 00-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                @endif
            </div>
            @if($handle || $location)
                <p class="text-[11px] text-[var(--text3)] truncate">
                    {{ $handle ? '@'.$handle : '' }}{{ $handle && $location ? ' · ' : '' }}{{ $location }}
                </p>
            @endif
        </div>
        {{ $slot }}
    </div>

@else
    {{-- Full card --}}
    <div {{ $attributes->merge(['class' => 'bg-[var(--surface)] border border-[var(--border)] rounded-[var(--radius-xl)] overflow-hidden shadow-[var(--shadow)]']) }}>
        {{-- Cover --}}
        <div class="h-16 bg-gradient-to-r {{ $coverGradient }}"></div>

        <div class="px-4 pb-4 -mt-6">
            {{-- Avatar row --}}
            <div class="flex items-end justify-between mb-3">
                @if($src)
                    <img src="{{ $src }}" alt="{{ $name }}"
                        class="w-12 h-12 rounded-full border-3 border-[var(--surface)] object-cover shadow"/>
                @else
                    <div class="w-12 h-12 rounded-full border-3 border-[var(--surface)] bg-gradient-to-br from-blue-400 to-violet-600 flex items-center justify-center text-white font-bold text-base shadow">
                        {{ $initials }}
                    </div>
                @endif
                @if($actions)
                    <div class="flex gap-1.5 mb-0.5">
                        <button class="h-7 px-3 text-[12px] font-medium bg-[var(--accent)] text-white rounded-[var(--radius)] hover:opacity-88 transition-opacity">Follow</button>
                        <button class="h-7 w-7 flex items-center justify-center rounded-[var(--radius)] border border-[var(--border2)] bg-[var(--surface)] text-[var(--text3)] hover:bg-[var(--bg2)]">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 012-2h1.4l.43 1.718a4 4 0 001.357 2.142L9 6.94V13a2 2 0 01-2 2H5a2 2 0 01-2-2V4zM11 13V6.94l.813-.08A4 4 0 0013.17 5.72L13.6 4H15a2 2 0 012 2v9a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Name / handle / location --}}
            <div class="flex items-center gap-1.5 mb-0.5">
                <h3 class="text-[15px] font-semibold text-[var(--text)] tracking-tight">{{ $name }}</h3>
                @if($verified)
                    <svg class="w-4 h-4 text-[var(--accent-text)] shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.403 12.652a3 3 0 000-5.304 3 3 0 00-3.75-3.751 3 3 0 00-5.305 0 3 3 0 00-3.751 3.75 3 3 0 000 5.305 3 3 0 003.75 3.751 3 3 0 005.305 0 3 3 0 003.751-3.75zm-2.546-4.46a.75.75 0 00-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                @endif
            </div>
            @if($handle || $location)
                <p class="text-[12px] text-[var(--text3)] mb-2">
                    {{ $handle ? '@'.$handle : '' }}{{ $handle && $location ? ' · ' : '' }}{{ $location }}
                </p>
            @endif

            {{-- Bio --}}
            @if($bio)
                <p class="text-[12px] text-[var(--text2)] leading-relaxed mb-3">{{ $bio }}</p>
            @endif

            {{-- Tags --}}
            @if(!empty($tags))
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @foreach($tags as $tag)
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[var(--bg2)] text-[var(--text2)] border border-[var(--border)]">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Stats --}}
            @if(!empty($stats))
                <div class="flex gap-4 pt-2 border-t border-[var(--border)]">
                    @foreach($stats as $stat)
                        <div>
                            <p class="text-[14px] font-bold text-[var(--text)]">{{ $stat['value'] }}</p>
                            <p class="text-[10px] text-[var(--text3)]">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Custom slot --}}
            @if(!$slot->isEmpty())
                <div class="mt-3">{{ $slot }}</div>
            @endif
        </div>
    </div>
@endif
