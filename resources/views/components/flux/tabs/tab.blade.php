{{-- tabs/tab.blade.php --}}
@props([
    'name',
    'icon'     => null,
    'badge'    => null,
    'disabled' => false,
])

{{-- Determine variant from closest tabs ancestor --}}
<button
    type="button"
    role="tab"
    :id="'tab-{{ $name }}'"
    :aria-controls="'panel-{{ $name }}'"
    :aria-selected="(activeTab === '{{ $name }}').toString()"
    :tabindex="activeTab === '{{ $name }}' ? 0 : -1"
    @click="!{{ $disabled ? 'true' : 'false' }} && setTab('{{ $name }}')"
    @keydown.arrow-right.prevent="nextTab()"
    @keydown.arrow-left.prevent="prevTab()"
    @keydown.home.prevent="firstTab()"
    @keydown.end.prevent="lastTab()"
    :disabled="{{ $disabled ? 'true' : 'false' }}"
    {{ $attributes->merge(['class' => '
        relative inline-flex items-center gap-1.5 px-3.5 py-2 text-[13px] font-medium whitespace-nowrap
        transition-all duration-100 outline-none cursor-pointer select-none
        focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[var(--accent)]
        disabled:opacity-40 disabled:pointer-events-none
    ']) }}
    {{-- Dynamic classes injected per variant via x-bind --}}
    x-bind:class="{
        /* line variant */
        'border-b-2 -mb-px border-[var(--accent)] text-[var(--accent-text)]':
            activeTab === '{{ $name }}' && variant === 'line',
        'border-b-2 -mb-px border-transparent text-[var(--text2)] hover:text-[var(--text)] hover:border-[var(--border2)]':
            activeTab !== '{{ $name }}' && variant === 'line',

        /* pill variant */
        'bg-[var(--surface)] text-[var(--text)] shadow-[var(--shadow)] rounded-[var(--radius)]':
            activeTab === '{{ $name }}' && variant === 'pill',
        'text-[var(--text2)] hover:text-[var(--text)] rounded-[var(--radius)]':
            activeTab !== '{{ $name }}' && variant === 'pill',

        /* box variant */
        'bg-[var(--accent)] text-white border-r border-[var(--border)] last:border-r-0':
            activeTab === '{{ $name }}' && variant === 'box',
        'text-[var(--text2)] hover:bg-[var(--bg2)] hover:text-[var(--text)] border-r border-[var(--border)] last:border-r-0':
            activeTab !== '{{ $name }}' && variant === 'box',
    }"
>
    @if($icon)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-4 h-4 shrink-0" aria-hidden="true"/>
    @endif

    <span>{{ $slot }}</span>

    @if($badge)
        <span
            class="ml-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold rounded-full leading-none"
            x-bind:class="activeTab === '{{ $name }}'
                ? 'bg-[var(--accent)] text-white'
                : 'bg-[var(--bg2)] text-[var(--text2)]'"
        >
            {{ $badge }}
        </span>
    @endif
</button>
