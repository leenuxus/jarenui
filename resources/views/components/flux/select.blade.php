@props([
    'label'       => null,
    'hint'        => null,
    'error'       => null,
    'placeholder' => 'Select an option…',
    'options'     => [],      // ['value' => 'Label'] or [['value'=>'','label'=>'','group'=>'']]
    'multiple'    => false,
    'searchable'  => false,
    'clearable'   => false,
    'size'        => 'md',    // sm | md | lg
    'id'          => null,
])

@php
$selectId = $id ?? 'select-' . \Illuminate\Support\Str::random(6);

$sizes = [
    'sm' => 'h-8 text-xs px-2.5',
    'md' => 'h-[34px] text-[13px] px-3',
    'lg' => 'h-10 text-sm px-3.5',
];

$baseClass = implode(' ', [
    'w-full appearance-none bg-[var(--surface)] text-[var(--text)]',
    'border rounded-[var(--radius)] outline-none transition-all duration-150',
    'focus:ring-2 focus:ring-[var(--accent)]/20 focus:border-[var(--accent)]',
    'disabled:opacity-50 disabled:cursor-not-allowed',
    'pr-8',
    $sizes[$size] ?? $sizes['md'],
    $error ? 'border-[var(--danger)]' : 'border-[var(--border2)]',
]);

// Normalise options to [['value','label','group'?,'disabled'?]]
$normalised = [];
foreach ($options as $k => $v) {
    if (is_array($v)) {
        $normalised[] = $v;
    } else {
        $normalised[] = ['value' => $k, 'label' => $v];
    }
}

// Group options
$groups   = [];
$noGroup  = [];
foreach ($normalised as $opt) {
    if (!empty($opt['group'])) {
        $groups[$opt['group']][] = $opt;
    } else {
        $noGroup[] = $opt;
    }
}
@endphp

<div
    {{ $attributes->only('class','wire:key')->merge(['class' => 'flex flex-col gap-1']) }}
    @if($searchable) x-data="jarenSelect()" @endif
>
    @if($label)
        <label for="{{ $selectId }}" class="text-xs font-medium text-[var(--text2)] leading-none">
            {{ $label }}
            @if($attributes->has('required'))
                <span class="text-[var(--danger-text)] ml-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($searchable)
            {{-- ── Searchable custom select ─────────────────────────────────────── --}}
            <div
                x-data="jarenSelect(@js($normalised), @js($placeholder))"
                class="relative"
                x-on:keydown.escape="close()"
                x-on:click.outside="close()"
            >
                {{-- Trigger button --}}
                <button
                    type="button"
                    @click="toggle()"
                    :aria-expanded="open.toString()"
                    aria-haspopup="listbox"
                    class="{{ $baseClass }} flex items-center justify-between cursor-pointer text-left"
                    :class="!selected ? 'text-[var(--text3)]' : ''"
                >
                    <span x-text="selected ? selected.label : placeholder" class="truncate flex-1"></span>
                    <span class="pointer-events-none ml-1 text-[var(--text3)]">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="m6 8 4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </button>

                {{-- Dropdown panel --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 translate-y-[-4px]"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute z-50 mt-1 w-full bg-[var(--surface)] border border-[var(--border2)] rounded-[var(--radius-lg)] shadow-[var(--shadow-lg)] overflow-hidden"
                    role="listbox"
                    :aria-label="'{{ $label ?? 'Options' }}'"
                >
                    {{-- Search --}}
                    <div class="flex items-center gap-2 p-2 border-b border-[var(--border)]">
                        <svg class="w-4 h-4 text-[var(--text3)] shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                        </svg>
                        <input
                            type="text"
                            x-model="query"
                            placeholder="Search…"
                            class="flex-1 bg-transparent text-[13px] text-[var(--text)] placeholder-[var(--text3)] outline-none border-none"
                            @keydown.arrow-down.prevent="focusNext()"
                            @keydown.arrow-up.prevent="focusPrev()"
                            @keydown.enter.prevent="selectFocused()"
                        >
                        <button type="button" x-show="query" @click="query=''" class="text-[var(--text3)] hover:text-[var(--text)]" aria-label="Clear search">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
                        </button>
                    </div>

                    {{-- Options list --}}
                    <ul class="max-h-52 overflow-y-auto py-1" role="listbox">
                        <template x-for="(opt, i) in filtered" :key="opt.value">
                            <li
                                role="option"
                                :aria-selected="selected?.value === opt.value"
                                :data-focused="focusedIndex === i"
                                @click="selectOption(opt)"
                                @mouseenter="focusedIndex = i"
                                class="flex items-center gap-2.5 px-3 py-2 text-[13px] cursor-pointer transition-colors"
                                :class="{
                                    'bg-[var(--accent-bg)] text-[var(--accent-text)]': selected?.value === opt.value,
                                    'bg-[var(--bg2)]': focusedIndex === i && selected?.value !== opt.value,
                                    'text-[var(--text)]': selected?.value !== opt.value,
                                }"
                            >
                                {{-- Checkmark --}}
                                <span class="w-4 h-4 shrink-0">
                                    <svg x-show="selected?.value === opt.value" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                <span x-text="opt.label" class="flex-1 truncate"></span>
                            </li>
                        </template>
                        <li x-show="filtered.length === 0" class="px-3 py-4 text-[13px] text-[var(--text3)] text-center">
                            No options found
                        </li>
                    </ul>

                    @if($clearable)
                    <div class="border-t border-[var(--border)] p-1.5">
                        <button
                            type="button"
                            @click="selected = null; close()"
                            class="w-full text-[12px] text-[var(--text3)] hover:text-[var(--text)] py-1 rounded hover:bg-[var(--bg2)] transition-colors"
                        >
                            Clear selection
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Hidden native input for form submission --}}
                <input type="hidden" :name="'{{ $attributes->get('name') }}'" :value="selected?.value ?? ''">
            </div>

        @else
            {{-- ── Native <select> ─────────────────────────────────────────────── --}}
            <select
                id="{{ $selectId }}"
                {{ $attributes->except(['class','label','hint','error','placeholder','options','searchable','clearable','size','id'])->merge(['class' => $baseClass]) }}
                @if($multiple) multiple @endif
                aria-describedby="{{ $error ? $selectId.'-desc' : '' }}"
                aria-invalid="{{ $error ? 'true' : 'false' }}"
            >
                @if($placeholder && !$multiple)
                    <option value="" disabled {{ !$attributes->get('wire:model') && !$attributes->get('value') ? 'selected' : '' }}>
                        {{ $placeholder }}
                    </option>
                @endif

                @if(!empty($slot->toHtml()))
                    {{ $slot }}
                @else
                    @foreach($noGroup as $opt)
                        <option
                            value="{{ $opt['value'] }}"
                            @if(!empty($opt['disabled'])) disabled @endif
                        >{{ $opt['label'] }}</option>
                    @endforeach

                    @foreach($groups as $groupName => $groupOpts)
                        <optgroup label="{{ $groupName }}">
                            @foreach($groupOpts as $opt)
                                <option
                                    value="{{ $opt['value'] }}"
                                    @if(!empty($opt['disabled'])) disabled @endif
                                >{{ $opt['label'] }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                @endif
            </select>

            {{-- Chevron overlay --}}
            <span class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center text-[var(--text3)]" aria-hidden="true">
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="m6 8 4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        @endif
    </div>

    @if($error)
        <p id="{{ $selectId }}-desc" class="text-[11px] text-[var(--danger-text)]" role="alert">{{ $error }}</p>
    @elseif($hint)
        <p id="{{ $selectId }}-desc" class="text-[11px] text-[var(--text3)]">{{ $hint }}</p>
    @endif
</div>

@once
<script>
function jarenSelect(options = [], placeholder = 'Select…') {
    return {
        open: false,
        query: '',
        selected: null,
        focusedIndex: -1,
        options,
        placeholder,
        get filtered() {
            if (!this.query) return this.options;
            const q = this.query.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },
        toggle() { this.open = !this.open; if (this.open) this.$nextTick(() => this.$el.querySelector('input')?.focus()); },
        close() { this.open = false; this.query = ''; this.focusedIndex = -1; },
        selectOption(opt) { this.selected = opt; this.close(); this.$dispatch('jaren-select', { value: opt.value }); },
        focusNext() { this.focusedIndex = Math.min(this.focusedIndex + 1, this.filtered.length - 1); },
        focusPrev() { this.focusedIndex = Math.max(this.focusedIndex - 1, 0); },
        selectFocused() { if (this.filtered[this.focusedIndex]) this.selectOption(this.filtered[this.focusedIndex]); },
    };
}
</script>
@endonce
