@props([
    'label'       => null,
    'placeholder' => 'Add tag…',
    'maxTags'     => null,
    'allowDupes'  => false,
    'suggestions' => [],     // string[] for autocomplete
    'id'          => null,
])

@php $pillboxId = $id ?? 'pillbox-' . \Illuminate\Support\Str::random(6); @endphp

<div
    {{ $attributes->only('class','wire:key')->merge(['class' => 'flex flex-col gap-1']) }}
    x-data="jarenPillbox(@js($suggestions), {{ $allowDupes ? 'true' : 'false' }}, {{ $maxTags ?? 'null' }})"
>
    @if($label)
        <label for="{{ $pillboxId }}-input" class="text-xs font-medium text-[var(--text2)]">
            {{ $label }}
        </label>
    @endif

    {{-- Pill container --}}
    <div
        class="flex flex-wrap gap-1.5 p-2 min-h-[38px] bg-[var(--surface)] border border-[var(--border2)] rounded-[var(--radius)] transition-all cursor-text
            focus-within:ring-2 focus-within:ring-[var(--accent)]/20 focus-within:border-[var(--accent)]"
        @click="$refs.input.focus()"
    >
        {{-- Existing pills --}}
        <template x-for="(tag, i) in tags" :key="i">
            <span
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-[var(--accent-bg)] text-[var(--accent-text)] text-[12px] font-medium"
                x-text="''"
            >
                <span x-text="tag"></span>
                <button
                    type="button"
                    @click.stop="removeTag(i)"
                    class="opacity-60 hover:opacity-100 transition-opacity leading-none focus-visible:outline-none"
                    :aria-label="`Remove ${tag}`"
                >
                    <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 1l10 10M11 1L1 11"/></svg>
                </button>
            </span>
        </template>

        {{-- Input --}}
        <input
            id="{{ $pillboxId }}-input"
            x-ref="input"
            x-model="inputVal"
            type="text"
            placeholder="{{ $placeholder }}"
            @keydown.enter.prevent="addTag()"
            @keydown.comma.prevent="addTag()"
            @keydown.tab="inputVal && addTag()"
            @keydown.backspace="!inputVal && removeTag(tags.length - 1)"
            :disabled="maxTags !== null && tags.length >= maxTags"
            class="flex-1 min-w-[80px] bg-transparent text-[13px] text-[var(--text)] placeholder-[var(--text3)] border-none outline-none disabled:cursor-not-allowed"
            :aria-label="'{{ $label ?? 'Tags' }}'"
            autocomplete="off"
        >
    </div>

    {{-- Suggestions dropdown --}}
    <template x-if="filteredSuggestions.length > 0 && inputVal.length > 0">
        <div class="mt-0.5 bg-[var(--surface)] border border-[var(--border2)] rounded-[var(--radius-lg)] shadow-[var(--shadow-lg)] overflow-hidden z-10">
            <template x-for="s in filteredSuggestions" :key="s">
                <div
                    @click="selectSuggestion(s)"
                    class="px-3 py-2 text-[13px] text-[var(--text)] cursor-pointer hover:bg-[var(--bg2)] transition-colors"
                    x-text="s"
                ></div>
            </template>
        </div>
    </template>

    {{-- Hidden form field --}}
    <input
        type="hidden"
        :name="'{{ $attributes->get('name', 'tags') }}[]'"
        x-bind:value="tags.join(',')"
        {{ $attributes->except(['class','wire:key','label','placeholder','max-tags','allow-dupes','suggestions','id','name']) }}
    >

    {{-- Count hint --}}
    <template x-if="maxTags !== null">
        <p class="text-[11px] text-[var(--text3)]">
            <span x-text="tags.length"></span> / <span x-text="maxTags"></span> tags
        </p>
    </template>
</div>

@once
<script>
function jarenPillbox(suggestions = [], allowDupes = false, maxTags = null) {
    return {
        tags: [],
        inputVal: '',
        maxTags,
        suggestions,
        get filteredSuggestions() {
            if (!this.inputVal) return [];
            const q = this.inputVal.toLowerCase();
            return this.suggestions.filter(s =>
                s.toLowerCase().includes(q) && (allowDupes || !this.tags.includes(s))
            ).slice(0, 6);
        },
        addTag() {
            const tag = this.inputVal.replace(/,$/, '').trim();
            if (!tag) return;
            if (!allowDupes && this.tags.includes(tag)) { this.inputVal = ''; return; }
            if (maxTags !== null && this.tags.length >= maxTags) return;
            this.tags.push(tag);
            this.inputVal = '';
            this.$dispatch('jaren-pillbox-change', { tags: this.tags });
        },
        removeTag(index) {
            this.tags.splice(index, 1);
            this.$dispatch('jaren-pillbox-change', { tags: this.tags });
        },
        selectSuggestion(s) {
            this.inputVal = s;
            this.addTag();
            this.$refs.input.focus();
        },
    };
}
</script>
@endonce
