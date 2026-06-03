{{--
  JarenUI AsyncCombobox — server-side search.
  Rendered by JarenUI\Livewire\AsyncCombobox.
--}}

@php
use JarenUI\Support\SizeResolver;
$sz = SizeResolver::input($size);
$cbId = 'async-cb-' . \Illuminate\Support\Str::random(6);
@endphp

<div
    class="flex flex-col gap-1"
    x-data="jarenAsyncCombobox({
        wireId: $wire.__instance.id,
        multiple: {{ $multiple ? 'true' : 'false' }},
    })"
    x-on:keydown.escape="close()"
    x-on:click.outside="close()"
>
    @if($label)
        <label
            for="{{ $cbId }}-input"
            class="{{ $sz['label'] }} font-medium text-[var(--text2)] leading-none"
        >
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        {{-- Trigger --}}
        <div
            class="flex flex-wrap items-center gap-1 min-h-[36px] px-3 py-1.5 {{ $sz['radius'] }}
                border bg-[var(--surface)] cursor-text transition-all duration-150
                focus-within:ring-2 focus-within:ring-[var(--accent)]/20 focus-within:border-[var(--accent)]
                {{ $error ? 'border-[var(--danger)]' : 'border-[var(--border2)]' }}"
            :class="open ? 'ring-2 ring-[var(--accent)]/20 border-[var(--accent)]' : ''"
            @click.self="$refs.input.focus()"
        >
            {{-- Selected pills (multiple mode) --}}
            @if($multiple && !empty($selected))
                @foreach($selected as $sel)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[12px] font-medium rounded bg-[var(--bg2)] border border-[var(--border)] text-[var(--text)]">
                        {{ $sel['label'] }}
                        <button
                            type="button"
                            wire:click="deselectOption('{{ $sel['value'] }}')"
                            class="opacity-50 hover:opacity-100 transition-opacity leading-none"
                            aria-label="Remove {{ $sel['label'] }}"
                        >
                            <svg class="w-3 h-3" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 1l8 8M9 1L1 9"/></svg>
                        </button>
                    </span>
                @endforeach
            @elseif(!$multiple && $selected)
                <div class="flex-1 flex items-center gap-2 pointer-events-none {{ $sz['text'] }} text-[var(--text)]">
                    @if($withAvatars && isset($selected['initials']))
                        <span
                            class="w-5 h-5 rounded-full flex items-center justify-center text-white font-bold text-[10px] shrink-0"
                            style="background: {{ $selected['color'] ?? 'var(--accent)' }}"
                        >{{ $selected['initials'] }}</span>
                    @endif
                    <span>{{ $selected['label'] }}</span>
                </div>
            @endif

            {{-- Search input --}}
            <input
                id="{{ $cbId }}-input"
                x-ref="input"
                type="text"
                wire:model.live.debounce.300ms="query"
                @input="open = true"
                @focus="open = true"
                @keydown.arrow-down.prevent="focusNext()"
                @keydown.arrow-up.prevent="focusPrev()"
                @keydown.enter.prevent="selectFocused()"
                @keydown.backspace="onBackspace()"
                placeholder="{{ !$multiple && $selected ? '' : $placeholder }}"
                class="flex-1 min-w-[80px] border-none outline-none bg-transparent {{ $sz['text'] }} text-[var(--text)] placeholder-[var(--text3)] font-[inherit] leading-none"
                autocomplete="off"
            >

            {{-- Right icons --}}
            <div class="flex items-center gap-1 ml-auto shrink-0">
                {{-- Spinner --}}
                <svg
                    wire:loading
                    wire:target="doSearch"
                    class="{{ $sz['icon'] }} animate-spin text-[var(--text3)]"
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>

                @if(!$multiple && $selected)
                    <button
                        type="button"
                        wire:click="clearAll"
                        class="w-5 h-5 flex items-center justify-center rounded-full text-[var(--text3)] hover:bg-[var(--bg2)] hover:text-[var(--text)] transition-colors"
                        aria-label="Clear"
                    >
                        <svg class="{{ $sz['icon'] }}" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 1l12 12M13 1L1 13"/></svg>
                    </button>
                @endif

                <button
                    type="button"
                    @click.stop="toggle()"
                    class="w-5 h-5 flex items-center justify-center text-[var(--text3)] hover:text-[var(--text)] transition-colors"
                    tabindex="-1"
                >
                    <svg class="{{ $sz['icon'] }} transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 8 4 4 4-4"/></svg>
                </button>
            </div>
        </div>

        {{-- Dropdown --}}
        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 -translate-y-1 scale-[.98]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="absolute z-50 mt-1 w-full bg-[var(--surface)] border border-[var(--border2)] rounded-[var(--radius-lg)] shadow-[var(--shadow-lg)] overflow-hidden"
        >
            <ul class="max-h-56 overflow-y-auto py-1">
                {{-- Loading --}}
                <li wire:loading wire:target="doSearch" class="px-3 py-4 text-center text-[13px] text-[var(--text3)]">
                    <span class="inline-flex items-center gap-2">
                        <svg class="{{ $sz['icon'] }} animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Searching…
                    </span>
                </li>

                @forelse($results as $i => $opt)
                    @php
                        $isSelected = $multiple
                            ? collect($selected ?? [])->contains('value', $opt['value'])
                            : ($selected['value'] ?? null) == $opt['value'];
                    @endphp
                    <li
                        wire:key="cbopt-{{ $opt['value'] }}"
                        wire:click="selectOption('{{ $opt['value'] }}', '{{ addslashes($opt['label']) }}')"
                        x-on:mouseenter="focusedIndex = {{ $i }}"
                        class="flex items-center gap-2.5 px-3 py-2 text-[13px] cursor-pointer transition-colors select-none"
                        :class="{
                            'bg-[var(--accent-bg)] text-[var(--accent-text)]': {{ $isSelected ? 'true' : 'false' }} && focusedIndex !== {{ $i }},
                            'bg-[var(--bg2)]': focusedIndex === {{ $i }} && !{{ $isSelected ? 'true' : 'false' }},
                            'bg-[var(--accent)] text-white': focusedIndex === {{ $i }} && {{ $isSelected ? 'true' : 'false' }},
                            'text-[var(--text)]': !{{ $isSelected ? 'true' : 'false' }},
                        }"
                        role="option"
                        aria-selected="{{ $isSelected ? 'true' : 'false' }}"
                    >
                        @if($withAvatars && isset($opt['initials']))
                            <span
                                class="w-6 h-6 rounded-full flex items-center justify-center text-white font-bold text-[10px] shrink-0"
                                style="background: {{ $opt['color'] ?? 'var(--accent)' }}"
                                aria-hidden="true"
                            >{{ $opt['initials'] }}</span>
                        @else
                            <span class="w-4 h-4 shrink-0 flex items-center justify-center" aria-hidden="true">
                                @if($isSelected)
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                                @endif
                            </span>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="truncate">{{ $opt['label'] }}</div>
                            @if($withDescriptions && isset($opt['description']))
                                <div class="text-[11px] opacity-70 truncate mt-px">{{ $opt['description'] }}</div>
                            @endif
                        </div>

                        @if(isset($opt['meta']))
                            <span class="text-[11px] text-[var(--text3)] shrink-0">{{ $opt['meta'] }}</span>
                        @endif

                        @if($withBadges && isset($opt['badge']))
                            <span class="text-[10px] font-semibold px-1.5 py-px rounded-full bg-[var(--accent-bg)] text-[var(--accent-text)] shrink-0">{{ $opt['badge'] }}</span>
                        @endif
                    </li>
                @empty
                    <li wire:loading.remove wire:target="doSearch" class="px-3 py-5 text-center text-[13px] text-[var(--text3)]">
                        @if(strlen($query) < $minChars)
                            Type to search…
                        @else
                            No results for "{{ $query }}"
                        @endif
                    </li>
                @endforelse
            </ul>

            @if($multiple && !empty($selected))
                <div class="px-3 py-2 border-t border-[var(--border)] flex items-center justify-between text-[11px] text-[var(--text3)]">
                    <span>{{ count($selected) }} selected</span>
                    <button type="button" wire:click="clearAll" class="text-[var(--accent-text)] font-medium hover:underline">Clear all</button>
                </div>
            @endif
        </div>

        {{-- Hidden inputs --}}
        @if($multiple)
            @foreach($selected ?? [] as $sel)
                <input type="hidden" name="{{ $attributes->get('name') }}[]" value="{{ $sel['value'] }}">
            @endforeach
        @elseif($selected)
            <input type="hidden" name="{{ $attributes->get('name') }}" value="{{ $selected['value'] }}"
                {{ $attributes->whereStartsWith('wire:model') }}>
        @endif
    </div>

    @if($error)
        <p class="{{ $sz['hint'] }} text-[var(--danger-text)]" role="alert">{{ $error }}</p>
    @elseif($hint)
        <p class="{{ $sz['hint'] }} text-[var(--text3)]">{{ $hint }}</p>
    @endif
</div>

@once
<script>
function jarenAsyncCombobox({ wireId, multiple }) {
    return {
        open:         false,
        focusedIndex: -1,
        multiple,

        toggle() { this.open ? this.close() : (this.open = true); },
        close()  { this.open = false; this.focusedIndex = -1; },

        focusNext() {
            const items = this.$el.querySelectorAll('[role="option"]');
            this.focusedIndex = Math.min(this.focusedIndex + 1, items.length - 1);
        },
        focusPrev() {
            this.focusedIndex = Math.max(this.focusedIndex - 1, 0);
        },
        selectFocused() {
            const items = this.$el.querySelectorAll('[role="option"]');
            if (items[this.focusedIndex]) {
                items[this.focusedIndex].click();
            }
        },
        onBackspace() {
            // Handled by Livewire's wire:click on pill × buttons
        },
    };
}
</script>
@endonce
