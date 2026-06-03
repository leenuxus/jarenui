@props([
    'label'         => null,
    'hint'          => null,
    'error'         => null,
    'placeholder'   => 'Select an option…',
    'searchPlaceholder' => 'Search…',

    // Mode
    'multiple'      => false,   // multi-select with pills
    'async'         => false,   // remote search (emits jaren-combobox-search event)

    // Options (ignored when async=true)
    // Array of: string | ['value','label'] | ['value','label','group','meta','badge','description','icon','disabled']
    'options'       => [],

    // Behaviour
    'searchable'    => true,    // show search input inside panel
    'clearable'     => true,    // show × clear button
    'closeOnSelect' => null,    // null = auto (false for multiple, true for single)
    'maxSelected'   => null,    // limit for multiple mode
    'creatable'     => false,   // allow creating new options by typing
    'creatableLabel'=> 'Create ":query"',

    // Display
    'size'          => null,    // xs|sm|md|lg|xl|2xl — inherits from form-size wrapper
    'id'            => null,
    'emptyText'     => 'No options found',
    'loadingText'   => 'Loading…',

    // Slots / custom rendering
    'withAvatars'   => false,   // show avatar initials from option['initials']
    'withBadges'    => false,   // show badge from option['badge']
    'withDescriptions' => false,// show description from option['description']
    'grouped'       => false,   // group options by option['group'] key
])

@php
use JarenUI\Support\SizeResolver;

$resolvedSize = SizeResolver::resolve($size ?? $jarenSize ?? null);
$sz           = SizeResolver::input($resolvedSize);
$comboId      = $id ?? 'cb-' . \Illuminate\Support\Str::random(8);

$closeOnSelect = $closeOnSelect ?? !$multiple;

// Normalise options → [['value', 'label', 'group'?, 'meta'?, 'badge'?, 'description'?, 'disabled'?]]
$normalised = [];
foreach ($options as $k => $v) {
    if (is_string($v))  { $normalised[] = ['value' => $k,         'label' => $v]; }
    elseif (is_array($v)) { $normalised[] = array_merge(['value' => $k, 'label' => $v['label'] ?? $k], $v); }
    else                { $normalised[] = ['value' => (string)$k, 'label' => (string)$v]; }
}

// Build group structure
$groups = [];
if ($grouped) {
    foreach ($normalised as $opt) {
        $g = $opt['group'] ?? '';
        $groups[$g][] = $opt;
    }
} else {
    $groups[''] = $normalised;
}

// Height / text / padding classes
$triggerClass = implode(' ', [
    'jaren-combobox-trigger relative flex flex-wrap items-center gap-1',
    'min-h-[' . match($resolvedSize) {
        'xs'  => '28px',
        'sm'  => '32px',
        'lg'  => '44px',
        'xl'  => '52px',
        '2xl' => '64px',
        default => '36px',
    } . ']',
    match($resolvedSize) {
        'xs', 'sm' => 'px-2 py-1',
        'lg'       => 'px-3.5 py-1.5',
        'xl'       => 'px-4 py-2',
        '2xl'      => 'px-5 py-2.5',
        default    => 'px-3 py-1.5',
    },
    $sz['radius'],
    'border bg-[var(--surface)] transition-all duration-150 cursor-text',
    'focus-within:ring-2 focus-within:ring-[var(--accent)]/20 focus-within:border-[var(--accent)]',
    $error
        ? 'border-[var(--danger)] focus-within:border-[var(--danger)] focus-within:ring-[var(--danger)]/15'
        : 'border-[var(--border2)]',
]);

$inputClass = implode(' ', [
    'flex-1 min-w-[60px] border-none outline-none bg-transparent',
    $sz['text'], 'text-[var(--text)] placeholder-[var(--text3)]',
    'font-[inherit] leading-none',
]);

$pillClass = implode(' ', [
    'inline-flex items-center gap-1 font-medium rounded-[4px]',
    'bg-[var(--bg2)] border border-[var(--border)] text-[var(--text)]',
    match($resolvedSize) {
        'xs','sm' => 'text-[11px] px-1.5 py-px',
        'lg'      => 'text-[13px] px-2 py-0.5',
        'xl','2xl'=> 'text-sm px-2.5 py-1',
        default   => 'text-[12px] px-2 py-0.5',
    },
]);

$iconSize = $sz['icon'];
@endphp

<div
    class="flex flex-col gap-1"
    {{ $attributes->only('class', 'wire:key') }}
>
    {{-- Label --}}
    @if($label)
        <label for="{{ $comboId }}-input" class="{{ $sz['label'] }} font-medium text-[var(--text2)] leading-none">
            {{ $label }}
            @if($attributes->has('required'))
                <span class="text-[var(--danger-text)] ml-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    {{-- Combobox --}}
    <div
        class="relative"
        x-data="jarenCombobox({
            multiple:      {{ $multiple ? 'true' : 'false' }},
            async:         {{ $async ? 'true' : 'false' }},
            options:       @js($normalised),
            grouped:       {{ $grouped ? 'true' : 'false' }},
            groups:        @js($groups),
            searchable:    {{ $searchable ? 'true' : 'false' }},
            clearable:     {{ $clearable ? 'true' : 'false' }},
            closeOnSelect: {{ $closeOnSelect ? 'true' : 'false' }},
            maxSelected:   @js($maxSelected),
            creatable:     {{ $creatable ? 'true' : 'false' }},
            creatableLabel:'{{ $creatableLabel }}',
            emptyText:     '{{ $emptyText }}',
            loadingText:   '{{ $loadingText }}',
            wireModel:     '{{ $attributes->whereStartsWith('wire:model')->first() }}',
        })"
        x-on:keydown.escape.window="close()"
        x-on:click.outside="close()"
        id="{{ $comboId }}"
        role="combobox"
        :aria-expanded="open.toString()"
        aria-haspopup="listbox"
        :aria-owns="'{{ $comboId }}-listbox'"
    >
        {{-- ── Trigger ─────────────────────────────────────────────── --}}
        <div
            class="{{ $triggerClass }}"
            :class="{ 'ring-2 ring-[var(--accent)]/20 border-[var(--accent)]': open }"
            @click="focusTrigger()"
        >
            {{-- Multi pills --}}
            @if($multiple)
                <template x-for="opt in selectedOptions" :key="opt.value">
                    <span class="{{ $pillClass }}">
                        @if($withAvatars)
                            <span
                                class="rounded-full flex items-center justify-center text-white font-bold shrink-0
                                    {{ match($resolvedSize) { 'xs','sm' => 'w-3.5 h-3.5 text-[8px]', 'lg','xl','2xl' => 'w-5 h-5 text-[10px]', default => 'w-4 h-4 text-[9px]' } }}"
                                :style="opt.color ? `background:${opt.color}` : 'background:var(--accent)'"
                                x-text="opt.initials || opt.label?.slice(0,2)?.toUpperCase()"
                                aria-hidden="true"
                            ></span>
                        @endif
                        <span x-text="opt.label" class="leading-none"></span>
                        @unless($attributes->get('disabled'))
                            <button
                                type="button"
                                @click.stop="deselect(opt)"
                                class="flex items-center leading-none opacity-50 hover:opacity-100 transition-opacity focus-visible:outline-none"
                                :aria-label="'Remove ' + opt.label"
                            >
                                <svg class="{{ match($resolvedSize) { 'xs','sm' => 'w-2.5 h-2.5', 'lg','xl','2xl' => 'w-3.5 h-3.5', default => 'w-3 h-3' } }}" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <path d="M1 1l8 8M9 1L1 9"/>
                                </svg>
                            </button>
                        @endunless
                    </span>
                </template>
            @else
                {{-- Single selected display --}}
                <div
                    x-show="selectedOptions.length > 0 && query === ''"
                    class="{{ $sz['text'] }} text-[var(--text)] leading-none flex items-center gap-2 flex-1 pointer-events-none"
                    aria-hidden="true"
                >
                    @if($withAvatars)
                        <template x-if="selectedOptions[0]">
                            <span
                                class="rounded-full flex items-center justify-center text-white font-bold shrink-0
                                    {{ match($resolvedSize) { 'xs','sm' => 'w-5 h-5 text-[10px]', 'lg','xl','2xl' => 'w-7 h-7 text-[13px]', default => 'w-6 h-6 text-[11px]' } }}"
                                :style="selectedOptions[0]?.color ? `background:${selectedOptions[0].color}` : 'background:var(--accent)'"
                                x-text="selectedOptions[0]?.initials || selectedOptions[0]?.label?.slice(0,2)?.toUpperCase()"
                            ></span>
                        </template>
                    @endif
                    <span x-text="selectedOptions[0]?.label"></span>
                </div>
            @endif

            {{-- Search / type input --}}
            <input
                id="{{ $comboId }}-input"
                x-ref="input"
                type="text"
                x-model="query"
                class="{{ $inputClass }}"
                :placeholder="selectedOptions.length === 0 ? '{{ $placeholder }}' : ''"
                :disabled="{{ $attributes->get('disabled') ? 'true' : 'false' }}"
                @focus="onFocus()"
                @input="onInput()"
                @keydown.arrow-down.prevent="focusNext()"
                @keydown.arrow-up.prevent="focusPrev()"
                @keydown.enter.prevent="selectFocused()"
                @keydown.backspace="onBackspace()"
                @keydown.tab="onTab($event)"
                autocomplete="off"
                autocorrect="off"
                spellcheck="false"
                :aria-activedescendant="focusedId"
                aria-autocomplete="list"
                :aria-controls="'{{ $comboId }}-listbox'"
            >

            {{-- Right controls --}}
            <div class="flex items-center gap-1 shrink-0 ml-auto">
                {{-- Loading spinner --}}
                <svg
                    x-show="loading"
                    x-cloak
                    class="{{ $iconSize }} animate-spin text-[var(--text3)]"
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>

                {{-- Clear button --}}
                @if($clearable)
                    <button
                        type="button"
                        x-show="selectedOptions.length > 0 && !loading"
                        x-cloak
                        @click.stop="clearAll()"
                        class="{{ match($resolvedSize) { 'xs','sm' => 'w-5 h-5', default => 'w-6 h-6' } }} flex items-center justify-center rounded-full text-[var(--text3)] hover:bg-[var(--bg2)] hover:text-[var(--text)] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)]"
                        aria-label="Clear selection"
                    >
                        <svg class="{{ $iconSize }}" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M1 1l12 12M13 1L1 13"/>
                        </svg>
                    </button>
                @endif

                {{-- Chevron --}}
                <button
                    type="button"
                    @click.stop="toggle()"
                    class="{{ match($resolvedSize) { 'xs','sm' => 'w-5 h-5', default => 'w-6 h-6' } }} flex items-center justify-center rounded text-[var(--text3)] hover:text-[var(--text)] transition-colors focus-visible:outline-none"
                    :aria-label="open ? 'Close' : 'Open'"
                    tabindex="-1"
                >
                    <svg
                        class="{{ $iconSize }} transition-transform duration-200"
                        :class="open ? 'rotate-180' : ''"
                        viewBox="0 0 20 20"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="m6 8 4 4 4-4"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Dropdown panel ──────────────────────────────────────── --}}
        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 translate-y-[-4px] scale-[.98]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-[-4px] scale-[.98]"
            class="absolute z-50 mt-1 w-full bg-[var(--surface)] border border-[var(--border2)] rounded-[var(--radius-lg)] shadow-[var(--shadow-lg)] overflow-hidden"
            id="{{ $comboId }}-listbox"
            role="listbox"
            :aria-label="'{{ $label ?? 'Options' }}'"
            :aria-multiselectable="{{ $multiple ? 'true' : 'false' }}"
        >
            {{-- Search box inside panel (when not async — async uses trigger input) --}}
            @if($searchable && !$async)
                <div class="flex items-center gap-2 px-3 py-2 border-b border-[var(--border)]">
                    <svg class="{{ $iconSize }} shrink-0 text-[var(--text3)]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                    </svg>
                    <input
                        x-ref="searchInput"
                        type="text"
                        x-model="query"
                        @input="onInput()"
                        @keydown.arrow-down.prevent="focusNext()"
                        @keydown.arrow-up.prevent="focusPrev()"
                        @keydown.enter.prevent="selectFocused()"
                        @keydown.escape="close()"
                        placeholder="{{ $searchPlaceholder }}"
                        class="flex-1 {{ $sz['text'] }} bg-transparent text-[var(--text)] placeholder-[var(--text3)] border-none outline-none font-[inherit]"
                        aria-label="{{ $searchPlaceholder }}"
                    >
                    <button
                        type="button"
                        x-show="query.length > 0"
                        @click="query = ''; onInput(); $refs.searchInput?.focus()"
                        class="text-[var(--text3)] hover:text-[var(--text)] transition-colors focus-visible:outline-none"
                        aria-label="Clear search"
                    >
                        <svg class="{{ $iconSize }}" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M1 1l12 12M13 1L1 13"/></svg>
                    </button>
                </div>
            @endif

            {{-- Options list --}}
            <ul
                class="max-h-56 overflow-y-auto py-1 overscroll-contain"
                role="group"
            >
                {{-- Loading state --}}
                <li x-show="loading" class="px-3 py-4 text-center text-[13px] text-[var(--text3)]">
                    <span class="inline-flex items-center gap-2">
                        <svg class="{{ $iconSize }} animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ $loadingText }}
                    </span>
                </li>

                {{-- Grouped options --}}
                <template x-if="!loading">
                    <div>
                        <template x-for="group in visibleGroups" :key="group.key">
                            <div>
                                {{-- Group label --}}
                                <template x-if="group.key !== ''">
                                    <li
                                        class="px-3 pt-2 pb-0.5 text-[10px] font-semibold uppercase tracking-[.07em] text-[var(--text3)]"
                                        role="presentation"
                                        x-text="group.key"
                                    ></li>
                                </template>

                                {{-- Options in group --}}
                                <template x-for="opt in group.options" :key="opt.value">
                                    <li
                                        :id="'{{ $comboId }}-opt-' + opt.value"
                                        role="option"
                                        :aria-selected="isSelected(opt).toString()"
                                        :aria-disabled="(opt.disabled || false).toString()"
                                        @click="!opt.disabled && selectOption(opt)"
                                        @mouseenter="!opt.disabled && (focusedValue = opt.value)"
                                        class="flex items-center gap-2.5 px-3 py-2 text-[13px] cursor-pointer transition-colors leading-tight select-none"
                                        :class="{
                                            'bg-[var(--accent-bg)] text-[var(--accent-text)]': isSelected(opt) && focusedValue !== opt.value,
                                            'bg-[var(--bg2)]': focusedValue === opt.value && !isSelected(opt),
                                            'bg-[var(--accent)] text-white': focusedValue === opt.value && isSelected(opt),
                                            'opacity-40 cursor-not-allowed pointer-events-none': opt.disabled,
                                            'text-[var(--text)]': !isSelected(opt),
                                        }"
                                    >
                                        {{-- Avatar --}}
                                        @if($withAvatars)
                                            <span
                                                class="w-6 h-6 rounded-full flex items-center justify-center text-white font-bold text-[10px] shrink-0"
                                                :style="opt.color ? `background:${opt.color}` : 'background:var(--accent)'"
                                                x-text="opt.initials || opt.label?.slice(0,2)?.toUpperCase()"
                                                aria-hidden="true"
                                            ></span>
                                        @else
                                            {{-- Checkmark slot --}}
                                            <span class="w-4 h-4 shrink-0 flex items-center justify-center" aria-hidden="true">
                                                <svg
                                                    x-show="isSelected(opt)"
                                                    class="w-4 h-4"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        @endif

                                        {{-- Label + optional description --}}
                                        <div class="flex-1 min-w-0">
                                            <span x-text="opt.label" class="block truncate"></span>
                                            @if($withDescriptions)
                                                <span
                                                    x-show="opt.description"
                                                    x-text="opt.description"
                                                    class="block text-[11px] opacity-70 truncate mt-px"
                                                ></span>
                                            @endif
                                        </div>

                                        {{-- Meta text --}}
                                        <span
                                            x-show="opt.meta"
                                            x-text="opt.meta"
                                            class="text-[11px] text-[var(--text3)] shrink-0 ml-auto"
                                        ></span>

                                        {{-- Badge --}}
                                        @if($withBadges)
                                            <span
                                                x-show="opt.badge"
                                                x-text="opt.badge"
                                                class="text-[10px] font-semibold px-1.5 py-px rounded-full shrink-0
                                                    bg-[var(--accent-bg)] text-[var(--accent-text)]"
                                            ></span>
                                        @endif
                                    </li>
                                </template>
                            </div>
                        </template>

                        {{-- Creatable option --}}
                        @if($creatable)
                            <li
                                x-show="query.trim() && !hasExactMatch"
                                @click="createOption()"
                                @mouseenter="focusedValue = '__create__'"
                                class="flex items-center gap-2 px-3 py-2 text-[13px] cursor-pointer transition-colors"
                                :class="focusedValue === '__create__' ? 'bg-[var(--bg2)]' : ''"
                            >
                                <svg class="{{ $iconSize }} text-[var(--accent-text)]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10.75 6.75a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z"/>
                                </svg>
                                <span class="text-[var(--accent-text)]">
                                    Create "<span x-text="query" class="font-medium"></span>"
                                </span>
                            </li>
                        @endif

                        {{-- Empty state --}}
                        <li
                            x-show="visibleGroups.every(g => g.options.length === 0) && !loading {{ $creatable ? "&& !query.trim()" : '' }}"
                            class="px-3 py-5 text-center text-[13px] text-[var(--text3)]"
                            role="status"
                        >
                            <svg class="w-8 h-8 mx-auto mb-2 text-[var(--border2)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                            {{ $emptyText }}
                        </li>
                    </div>
                </template>
            </ul>

            {{-- Footer slot --}}
            @if($multiple)
                <div class="px-3 py-2 border-t border-[var(--border)] flex items-center justify-between text-[11px] text-[var(--text3)]">
                    <span>
                        <span x-text="selectedOptions.length"></span> selected
                        @if($maxSelected)
                            <span>/ {{ $maxSelected }}</span>
                        @endif
                    </span>
                    <button
                        type="button"
                        x-show="selectedOptions.length > 0"
                        @click="clearAll()"
                        class="text-[var(--accent-text)] font-medium hover:underline focus-visible:outline-none"
                    >
                        Clear all
                    </button>
                </div>
            @endif

            {{-- Custom footer slot --}}
            @if(!$slot->isEmpty())
                <div class="border-t border-[var(--border)]">
                    {{ $slot }}
                </div>
            @endif
        </div>

        {{-- Hidden inputs for form submission --}}
        @if($multiple)
            <template x-for="opt in selectedOptions" :key="opt.value">
                <input type="hidden" name="{{ $attributes->get('name') }}[]" :value="opt.value">
            </template>
        @else
            <input
                type="hidden"
                name="{{ $attributes->get('name') }}"
                :value="selectedOptions[0]?.value ?? ''"
                {{ $attributes->whereStartsWith('wire:model') }}
            >
        @endif
    </div>

    {{-- Hint / error --}}
    @if($error)
        <p class="{{ $sz['hint'] }} text-[var(--danger-text)]" role="alert">{{ $error }}</p>
    @elseif($hint)
        <p class="{{ $sz['hint'] }} text-[var(--text3)]">{{ $hint }}</p>
    @endif
</div>

@once
<script>
function jarenCombobox(config) {
    return {
        // Config
        multiple:      config.multiple      ?? false,
        async:         config.async         ?? false,
        allOptions:    config.options       ?? [],
        grouped:       config.grouped       ?? false,
        allGroups:     config.groups        ?? { '': config.options ?? [] },
        searchable:    config.searchable    ?? true,
        clearable:     config.clearable     ?? true,
        closeOnSelect: config.closeOnSelect ?? !config.multiple,
        maxSelected:   config.maxSelected   ?? null,
        creatable:     config.creatable     ?? false,
        wireModel:     config.wireModel     ?? '',

        // State
        open:          false,
        query:         '',
        loading:       false,
        focusedValue:  null,
        selectedOptions: [],
        asyncResults:  [],

        // Debounce timer
        _debounce: null,

        // ── init ─────────────────────────────────────────────────────────────
        init() {
            // Sync with wire:model on mount
            this.$watch('selectedOptions', (val) => {
                if (!this.wireModel) return;
                const value = this.multiple
                    ? val.map(o => o.value)
                    : (val[0]?.value ?? null);
                this.$dispatch('input', value);
            });
        },

        // ── computed ─────────────────────────────────────────────────────────
        get visibleGroups() {
            const q = this.query.toLowerCase().trim();
            const source = this.async ? { '': this.asyncResults } : this.allGroups;

            return Object.entries(source).map(([key, opts]) => ({
                key,
                options: q
                    ? opts.filter(o =>
                        o.label?.toLowerCase().includes(q) ||
                        o.description?.toLowerCase().includes(q) ||
                        o.meta?.toLowerCase().includes(q)
                      )
                    : opts,
            }));
        },

        get hasExactMatch() {
            const q = this.query.trim().toLowerCase();
            return this.allOptions.some(o => o.label?.toLowerCase() === q);
        },

        get focusedId() {
            return this.focusedValue ? `cb-opt-${this.focusedValue}` : null;
        },

        get flatOptions() {
            return this.visibleGroups.flatMap(g => g.options);
        },

        // ── open / close ─────────────────────────────────────────────────────
        toggle() {
            this.open ? this.close() : this.openPanel();
        },

        openPanel() {
            this.open = true;
            this.$nextTick(() => {
                // Focus the search input inside the panel if not async
                if (this.searchable && !this.async) {
                    this.$refs.searchInput?.focus();
                }
            });
        },

        close() {
            this.open = false;
            this.query = '';
            this.focusedValue = null;
        },

        // ── focus handling ───────────────────────────────────────────────────
        focusTrigger() {
            this.$refs.input?.focus();
            if (!this.open) this.openPanel();
        },

        onFocus() {
            this.openPanel();
        },

        onInput() {
            this.open = true;
            this.focusedValue = null;

            if (this.async) {
                this.loading = true;
                clearTimeout(this._debounce);
                this._debounce = setTimeout(() => {
                    this.$dispatch('jaren-combobox-search', {
                        query:    this.query,
                        callback: (results) => {
                            this.asyncResults = results;
                            this.loading = false;
                        },
                    });
                }, 300);
            }
        },

        onBackspace() {
            if (this.query === '' && this.multiple && this.selectedOptions.length > 0) {
                this.deselect(this.selectedOptions[this.selectedOptions.length - 1]);
            }
        },

        onTab(event) {
            if (this.open && this.focusedValue) {
                event.preventDefault();
                this.selectFocused();
            }
        },

        // ── keyboard nav ─────────────────────────────────────────────────────
        focusNext() {
            if (!this.open) { this.openPanel(); return; }
            const opts  = this.flatOptions;
            const extra = this.creatable && this.query.trim() && !this.hasExactMatch ? ['__create__'] : [];
            const all   = [...opts.map(o => o.value), ...extra];
            const idx   = all.indexOf(this.focusedValue);
            this.focusedValue = all[Math.min(idx + 1, all.length - 1)] ?? all[0];
            this._scrollFocused();
        },

        focusPrev() {
            if (!this.open) return;
            const opts  = this.flatOptions;
            const extra = this.creatable && this.query.trim() && !this.hasExactMatch ? ['__create__'] : [];
            const all   = [...opts.map(o => o.value), ...extra];
            const idx   = all.indexOf(this.focusedValue);
            this.focusedValue = all[Math.max(idx - 1, 0)] ?? all[all.length - 1];
            this._scrollFocused();
        },

        selectFocused() {
            if (this.focusedValue === '__create__') { this.createOption(); return; }
            const opt = this.flatOptions.find(o => o.value === this.focusedValue);
            if (opt) this.selectOption(opt);
        },

        _scrollFocused() {
            this.$nextTick(() => {
                const el = this.$el.querySelector(`[id$="-opt-${this.focusedValue}"]`);
                el?.scrollIntoView({ block: 'nearest' });
            });
        },

        // ── selection ────────────────────────────────────────────────────────
        isSelected(opt) {
            return this.selectedOptions.some(s => s.value === opt.value);
        },

        selectOption(opt) {
            if (opt.disabled) return;

            if (this.multiple) {
                if (this.isSelected(opt)) {
                    this.deselect(opt);
                } else {
                    if (this.maxSelected && this.selectedOptions.length >= this.maxSelected) return;
                    this.selectedOptions = [...this.selectedOptions, opt];
                }
                this.query = '';
                this.$refs.input?.focus();
            } else {
                this.selectedOptions = [opt];
                this.query = '';
                if (this.closeOnSelect) this.close();
            }

            this.$dispatch('jaren-combobox-change', {
                value:   this.multiple ? this.selectedOptions.map(o => o.value) : opt.value,
                option:  this.multiple ? this.selectedOptions : opt,
            });
        },

        deselect(opt) {
            if (!opt) return;
            this.selectedOptions = this.selectedOptions.filter(s => s.value !== opt.value);
            this.$dispatch('jaren-combobox-change', {
                value:  this.selectedOptions.map(o => o.value),
                option: this.selectedOptions,
            });
        },

        clearAll() {
            this.selectedOptions = [];
            this.query = '';
            this.$refs.input?.focus();
            this.$dispatch('jaren-combobox-change', { value: this.multiple ? [] : null, option: null });
        },

        // ── creatable ────────────────────────────────────────────────────────
        createOption() {
            const label = this.query.trim();
            if (!label) return;
            const opt = { value: label.toLowerCase().replace(/\s+/g, '-'), label };
            this.allOptions = [...this.allOptions, opt];
            if (this.grouped) {
                this.allGroups[''] = [...(this.allGroups[''] ?? []), opt];
            }
            this.selectOption(opt);
            this.$dispatch('jaren-combobox-create', { value: opt.value, label: opt.label });
        },

        // ── async ────────────────────────────────────────────────────────────
        setAsyncResults(results) {
            this.asyncResults = results;
            this.loading = false;
        },
    };
}
</script>
@endonce
