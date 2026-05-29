@props([
    'title'    => '',
    'open'     => false,
    'disabled' => false,
    'icon'     => null,    // optional leading Heroicon
    'id'       => null,    // auto-generated if not provided
])

@php
$itemId = $id ?? 'accordion-' . \Illuminate\Support\Str::random(6);
@endphp

<div
    class="border-b border-[var(--border)] last:border-b-0"
    role="listitem"
    x-data="{
        id: '{{ $itemId }}',
        get isOpen() {
            return $root.closest('[x-data]').__x.$data.openItems.includes(this.id)
                || {{ $open ? 'true' : 'false' }};
        },
        init() {
            if ({{ $open ? 'true' : 'false' }}) {
                this.$root.closest('[x-data]').__x.$data.openItems.push(this.id);
            }
        },
        toggle() {
            const parent = this.$root.closest('[x-data]').__x.$data;
            if (parent.openItems.includes(this.id)) {
                parent.openItems = parent.openItems.filter(i => i !== this.id);
            } else {
                if (!parent.multiple) parent.openItems = [];
                parent.openItems.push(this.id);
            }
        }
    }"
    :class="isOpen ? 'bg-[var(--surface)]' : ''"
>
    {{-- Trigger --}}
    <button
        type="button"
        :id="id + '-trigger'"
        :aria-expanded="isOpen.toString()"
        :aria-controls="id + '-content'"
        :disabled="{{ $disabled ? 'true' : 'false' }}"
        @click="toggle()"
        @keydown.space.prevent="toggle()"
        @keydown.enter.prevent="toggle()"
        class="w-full flex items-center gap-3 px-4 py-3.5 text-left text-[13px] font-medium text-[var(--text)] bg-transparent border-none cursor-pointer transition-colors duration-100 hover:bg-[var(--bg2)] disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[var(--accent)]"
    >
        {{-- Optional leading icon --}}
        @if($icon)
            <x-dynamic-component
                :component="'heroicon-o-'.$icon"
                class="w-4 h-4 shrink-0 text-[var(--text3)]"
                aria-hidden="true"
            />
        @endif

        {{-- Title --}}
        <span class="flex-1">{{ $title }}</span>

        {{-- Chevron --}}
        <svg
            class="w-4 h-4 shrink-0 text-[var(--text3)] transition-transform duration-200"
            :class="isOpen ? 'rotate-180' : 'rotate-0'"
            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            aria-hidden="true"
        >
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </button>

    {{-- Content panel --}}
    <div
        :id="id + '-content'"
        :aria-labelledby="id + '-trigger'"
        role="region"
        x-show="isOpen"
        x-collapse
        class="overflow-hidden"
    >
        <div class="px-4 pb-4 pt-0 text-[13px] text-[var(--text2)] leading-relaxed">
            {{ $slot }}
        </div>
    </div>
</div>
