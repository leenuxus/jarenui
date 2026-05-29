{{--
  JarenUI Kanban Board — rendered by the Kanban Livewire component.
  Drag-and-drop via Alpine.js SortableJS integration.
--}}

@php
$tagColorMap = [
    'blue'   => 'bg-[var(--accent-bg)] text-[var(--accent-text)]',
    'red'    => 'bg-[var(--danger-bg)] text-[var(--danger-text)]',
    'green'  => 'bg-[var(--success-bg)] text-[var(--success-text)]',
    'yellow' => 'bg-[var(--warning-bg)] text-[var(--warning-text)]',
    'purple' => 'bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-300',
    'gray'   => 'bg-[var(--bg2)] text-[var(--text2)]',
];
@endphp

<div
    class="flex gap-3 overflow-x-auto pb-3 items-start"
    x-data="jarenKanban($wire)"
    wire:ignore.self
>
    @foreach($columns as $column)
        @php $colCards = $this->cardsForColumn($column['id']); @endphp

        <div
            class="flex flex-col gap-2 min-w-[200px] w-[220px] shrink-0 bg-[var(--bg2)] rounded-[var(--radius-lg)] p-2.5"
            wire:key="column-{{ $column['id'] }}"
        >
            {{-- Column header --}}
            <div class="flex items-center justify-between px-0.5 mb-1">
                <div class="flex items-center gap-2">
                    <span
                        class="w-2 h-2 rounded-full shrink-0"
                        style="background: {{ $column['color'] }}"
                        aria-hidden="true"
                    ></span>
                    <span class="text-[12px] font-semibold text-[var(--text2)]">
                        {{ $column['label'] }}
                    </span>
                </div>
                <div class="flex items-center gap-1.5">
                    @if(!empty($column['limit']))
                        <span
                            class="text-[10px] font-semibold px-1.5 py-px rounded-full border border-[var(--border2)] text-[var(--text3)]"
                            title="WIP limit: {{ $column['limit'] }}"
                        >
                            {{ count($colCards) }}/{{ $column['limit'] }}
                        </span>
                    @else
                        <span class="text-[10px] font-semibold px-1.5 py-px rounded-full bg-[var(--border2)] text-[var(--text3)]">
                            {{ count($colCards) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Drop zone + cards --}}
            <div
                class="flex flex-col gap-1.5 min-h-[60px]"
                x-data
                x-sortable
                x-sortable-group="kanban-cards"
                x-sortable-item-selector=".kb-card"
                @sortable:end="$wire.moveCard($event.detail.item.dataset.id, '{{ $column['id'] }}', $event.detail.previousItem?.dataset?.id)"
                data-column="{{ $column['id'] }}"
            >
                @foreach($colCards as $card)
                    <div
                        class="kb-card bg-[var(--surface)] border border-[var(--border)] rounded-[var(--radius)] p-2.5 cursor-grab active:cursor-grabbing transition-shadow hover:shadow-[var(--shadow-lg)] hover:border-[var(--border2)] group"
                        wire:key="card-{{ $card['id'] }}"
                        data-id="{{ $card['id'] }}"
                    >
                        {{-- Tag --}}
                        @if(!empty($card['tag']))
                            <span class="inline-flex items-center text-[10px] font-bold px-1.5 py-px rounded-full mb-1.5 {{ $tagColorMap[$card['tag_color'] ?? 'gray'] ?? $tagColorMap['gray'] }}">
                                {{ $card['tag'] }}
                            </span>
                        @endif

                        {{-- Title --}}
                        <p class="text-[12px] font-medium text-[var(--text)] leading-snug mb-2">
                            {{ $card['title'] }}
                        </p>

                        {{-- Footer: assignee + meta + delete --}}
                        <div class="flex items-center justify-between gap-1">
                            <div class="flex items-center gap-1.5">
                                {{-- Assignee avatar --}}
                                @if(!empty($card['assignee']))
                                    <span
                                        class="w-5 h-5 rounded-full flex items-center justify-center text-[8px] font-bold text-white shrink-0"
                                        style="background: {{ $card['assignee_color'] ?? '#6b7280' }}"
                                        title="{{ $card['assignee'] }}"
                                    >
                                        {{ $card['assignee'] }}
                                    </span>
                                @endif

                                {{-- Due date --}}
                                @if(!empty($card['due']))
                                    <span class="flex items-center gap-0.5 text-[10px] text-[var(--text3)]">
                                        <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($card['due'])->format('M j') }}
                                    </span>
                                @endif

                                {{-- Comment count --}}
                                @if(!empty($card['comments']))
                                    <span class="flex items-center gap-0.5 text-[10px] text-[var(--text3)]">
                                        <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 2c-2.236 0-4.43.18-6.57.524C1.993 2.755 1 4.014 1 5.426v5.148c0 1.413.993 2.67 2.43 2.902.848.137 1.705.248 2.57.331v3.443a.75.75 0 001.28.53l3.58-3.579c.91.05 1.83.07 2.76.07 2.236 0 4.43-.18 6.57-.524 1.437-.231 2.43-1.49 2.43-2.902V5.426c0-1.413-.993-2.67-2.43-2.902A41.102 41.102 0 0010 2z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $card['comments'] }}
                                    </span>
                                @endif
                            </div>

                            {{-- Delete (shown on hover) --}}
                            <button
                                type="button"
                                wire:click="deleteCard({{ $card['id'] }})"
                                wire:confirm="Delete this card?"
                                class="w-6 h-6 flex items-center justify-center rounded text-[var(--text3)] opacity-0 group-hover:opacity-100 hover:bg-[var(--danger-bg)] hover:text-[var(--danger-text)] transition-all focus-visible:opacity-100"
                                aria-label="Delete card"
                            >
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Add card form --}}
            @if($addingToColumn === $column['id'])
                <div class="mt-1" wire:key="add-form-{{ $column['id'] }}">
                    <textarea
                        wire:model="newCardTitle"
                        wire:keydown.enter.prevent="addCard"
                        wire:keydown.escape="cancelAdd"
                        autofocus
                        rows="2"
                        placeholder="Card title…"
                        class="w-full text-[13px] bg-[var(--surface)] text-[var(--text)] placeholder-[var(--text3)] border border-[var(--border2)] rounded-[var(--radius)] p-2 outline-none resize-none focus:ring-2 focus:ring-[var(--accent)]/20 focus:border-[var(--accent)]"
                    ></textarea>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <button
                            type="button"
                            wire:click="addCard"
                            class="h-7 px-3 text-[12px] font-medium bg-[var(--accent)] text-white rounded-[var(--radius)] hover:opacity-88 transition-opacity"
                        >
                            Add
                        </button>
                        <button
                            type="button"
                            wire:click="cancelAdd"
                            class="h-7 px-2 text-[12px] text-[var(--text3)] hover:text-[var(--text)] transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            @else
                <button
                    type="button"
                    wire:click="$set('addingToColumn', '{{ $column['id'] }}')"
                    class="w-full flex items-center justify-center gap-1.5 px-2 py-1.5 text-[12px] text-[var(--text3)] hover:text-[var(--text)] rounded-[var(--radius)] border border-dashed border-[var(--border2)] hover:bg-[var(--surface)] hover:border-[var(--border)] transition-all"
                >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 6.75a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z"/></svg>
                    Add card
                </button>
            @endif
        </div>
    @endforeach

    {{-- Add column placeholder --}}
    <button
        type="button"
        class="flex items-center gap-2 min-w-[180px] px-3 py-2.5 text-[13px] text-[var(--text3)] hover:text-[var(--text)] bg-[var(--bg2)]/60 hover:bg-[var(--bg2)] border border-dashed border-[var(--border2)] rounded-[var(--radius-lg)] transition-all"
        @click="$dispatch('jaren-kanban-add-column')"
    >
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 6.75a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z"/></svg>
        Add column
    </button>
</div>

@once
{{-- SortableJS CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js" defer></script>

<script>
function jarenKanban(wire) {
    return {
        wire,
        init() {
            this.$nextTick(() => this.initSortable());
        },
        initSortable() {
            const zones = this.$el.querySelectorAll('[x-sortable]');
            zones.forEach(zone => {
                Sortable.create(zone, {
                    group:     'kanban',
                    animation: 150,
                    ghostClass: 'opacity-40',
                    dragClass:  'shadow-xl rotate-1',
                    handle:    '.kb-card',
                    onEnd: (evt) => {
                        const cardId     = parseInt(evt.item.dataset.id);
                        const toColumn   = evt.to.closest('[data-column]')?.dataset?.column;
                        const afterCard  = evt.item.previousElementSibling?.dataset?.id ?? null;
                        if (toColumn) {
                            this.wire.moveCard(cardId, toColumn, afterCard ? parseInt(afterCard) : null);
                        }
                    },
                });
            });
        },
    };
}
</script>
@endonce
