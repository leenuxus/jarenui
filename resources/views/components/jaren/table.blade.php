{{--
  JarenUI Table — rendered by the Table Livewire component.
  Variables available: $rows (LengthAwarePaginator), $totalCount (int)
--}}

<div class="flex flex-col border border-[var(--border)] rounded-[var(--radius-lg)] bg-[var(--surface)] shadow-[var(--shadow)] overflow-hidden">

    {{-- ── Toolbar ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-2 px-3 py-2.5 border-b border-[var(--border)] bg-[var(--bg2)]">
        {{-- Search --}}
        <div class="relative flex items-center">
            <svg class="absolute left-2.5 w-3.5 h-3.5 text-[var(--text3)] pointer-events-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
            </svg>
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search…"
                class="h-8 pl-8 pr-3 text-[13px] bg-[var(--surface)] text-[var(--text)] placeholder-[var(--text3)] border border-[var(--border2)] rounded-[var(--radius)] outline-none focus:ring-2 focus:ring-[var(--accent)]/20 focus:border-[var(--accent)] w-48"
                aria-label="Search table"
            >
        </div>

        {{-- Selection count --}}
        @if(count($selected) > 0)
            <div class="flex items-center gap-2 ml-1">
                <span class="text-[12px] text-[var(--accent-text)] font-medium">
                    {{ count($selected) }} selected
                </span>
                <button
                    wire:click="clearSelection"
                    type="button"
                    class="text-[11px] text-[var(--text3)] hover:text-[var(--text)] transition-colors"
                >
                    Clear
                </button>
            </div>
        @endif

        {{-- Slot for custom toolbar buttons --}}
        <div class="ml-auto flex items-center gap-2">
            {{-- Per-page selector --}}
            <div class="flex items-center gap-1.5 text-[12px] text-[var(--text2)]">
                <span>Rows:</span>
                <select
                    wire:model.live="perPage"
                    class="h-7 px-1.5 text-[12px] bg-[var(--surface)] text-[var(--text)] border border-[var(--border2)] rounded-[var(--radius)] outline-none focus:ring-2 focus:ring-[var(--accent)]/20 appearance-none pr-5"
                    style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239a9894' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 4px center"
                >
                    @foreach($perPageOptions as $opt)
                        <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── Table ───────────────────────────────────────────────────────────── --}}
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-[13px]" role="grid">

            {{-- Head --}}
            <thead>
                <tr class="bg-[var(--bg2)]">
                    {{-- Select-all checkbox --}}
                    <th class="w-10 px-3 py-2.5 text-left" scope="col">
                        <div
                            role="checkbox"
                            :aria-checked="$wire.selectAll ? 'true' : ($wire.selected.length ? 'mixed' : 'false')"
                            tabindex="0"
                            wire:click="toggleSelectAll"
                            @keydown.space.prevent="$wire.toggleSelectAll()"
                            class="w-4 h-4 rounded border-[1.5px] flex items-center justify-center cursor-pointer transition-colors
                                {{ count($selected) > 0 ? 'bg-[var(--accent)] border-[var(--accent)]' : 'border-[var(--border2)] bg-[var(--surface)] hover:border-[var(--border2)]' }}"
                        >
                            @if($selectAll)
                                <svg class="w-2.5 h-2.5 text-white" viewBox="0 0 10 8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 4l3 3 5-5"/></svg>
                            @elseif(count($selected) > 0)
                                <svg class="w-2.5 h-2.5 text-white" viewBox="0 0 10 2" fill="currentColor"><rect x="0" y="0" width="10" height="2" rx="1"/></svg>
                            @endif
                        </div>
                    </th>

                    @foreach($columns as $col)
                        <th
                            scope="col"
                            class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[.06em] text-[var(--text3)] border-b border-[var(--border)] whitespace-nowrap
                                {{ !empty($col['class']) ? $col['class'] : '' }}
                                {{ !empty($col['sortable']) ? 'cursor-pointer hover:text-[var(--text2)] select-none' : '' }}"
                            @if(!empty($col['sortable']))
                                wire:click="sortBy('{{ $col['key'] }}')"
                                :aria-sort="sortColumn === '{{ $col['key'] }}' ? (sortDirection === 'asc' ? 'ascending' : 'descending') : 'none'"
                            @endif
                        >
                            <div class="flex items-center gap-1">
                                <span>{{ $col['label'] }}</span>
                                @if(!empty($col['sortable']))
                                    <span class="flex flex-col gap-[1px]" aria-hidden="true">
                                        <svg class="w-2 h-2 {{ $sortColumn === $col['key'] && $sortDirection === 'asc' ? 'text-[var(--accent-text)]' : 'text-[var(--border2)]' }}" viewBox="0 0 8 5" fill="currentColor"><path d="M4 0L8 5H0z"/></svg>
                                        <svg class="w-2 h-2 {{ $sortColumn === $col['key'] && $sortDirection === 'desc' ? 'text-[var(--accent-text)]' : 'text-[var(--border2)]' }}" viewBox="0 0 8 5" fill="currentColor"><path d="M4 5L0 0h8z"/></svg>
                                    </span>
                                @endif
                            </div>
                        </th>
                    @endforeach

                    {{-- Actions column --}}
                    <th scope="col" class="w-20 px-3 py-2.5 border-b border-[var(--border)]"></th>
                </tr>
            </thead>

            {{-- Body --}}
            <tbody class="divide-y divide-[var(--border)]">
                @forelse($rows as $row)
                    <tr
                        wire:key="row-{{ $row->id }}"
                        class="transition-colors hover:bg-[var(--bg2)] {{ in_array((string)$row->id, $selected) ? 'bg-[var(--accent-bg)]' : '' }}"
                    >
                        {{-- Row checkbox --}}
                        <td class="px-3 py-2.5">
                            <div
                                role="checkbox"
                                aria-checked="{{ in_array((string)$row->id, $selected) ? 'true' : 'false' }}"
                                tabindex="0"
                                wire:click="toggleSelect('{{ $row->id }}')"
                                @keydown.space.prevent="$wire.toggleSelect('{{ $row->id }}')"
                                class="w-4 h-4 rounded border-[1.5px] flex items-center justify-center cursor-pointer transition-colors
                                    {{ in_array((string)$row->id, $selected) ? 'bg-[var(--accent)] border-[var(--accent)]' : 'border-[var(--border2)] bg-[var(--surface)] hover:border-[var(--border2)]' }}"
                            >
                                @if(in_array((string)$row->id, $selected))
                                    <svg class="w-2.5 h-2.5 text-white" viewBox="0 0 10 8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 4l3 3 5-5"/></svg>
                                @endif
                            </div>
                        </td>

                        @foreach($columns as $col)
                            <td
                                class="px-3 py-2.5 text-[var(--text)] {{ !empty($col['class']) ? $col['class'] : '' }}"
                                wire:key="cell-{{ $row->id }}-{{ $col['key'] }}"
                            >
                                @if(isset($col['format']))
                                    {!! $this->formatCell(data_get($row, $col['key']), $col['format']) !!}
                                @else
                                    {{ data_get($row, $col['key']) ?? '—' }}
                                @endif
                            </td>
                        @endforeach

                        {{-- Row actions (override in subclass view with @section) --}}
                        <td class="px-3 py-2.5">
                            <div class="flex items-center gap-1 justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    type="button"
                                    wire:click="$dispatch('jaren-table-action', { action: 'edit', id: {{ $row->id }} })"
                                    class="w-7 h-7 flex items-center justify-center rounded-[var(--radius)] text-[var(--text3)] hover:bg-[var(--bg2)] hover:text-[var(--text)] transition-colors"
                                    aria-label="Edit row"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
                                </button>
                                <button
                                    type="button"
                                    wire:click="$dispatch('jaren-table-action', { action: 'delete', id: {{ $row->id }} })"
                                    class="w-7 h-7 flex items-center justify-center rounded-[var(--radius)] text-[var(--text3)] hover:bg-[var(--danger-bg)] hover:text-[var(--danger-text)] transition-colors"
                                    aria-label="Delete row"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 2 }}" class="px-4 py-12 text-center text-[13px] text-[var(--text3)]">
                            <svg class="w-8 h-8 mx-auto mb-3 text-[var(--border2)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                            </svg>
                            No records found
                            @if($search)
                                <span class="block mt-1 text-[12px]">Try adjusting your search term.</span>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Footer / Pagination ─────────────────────────────────────────────── --}}
    @if($rows->hasPages() || count($selected) > 0)
        <div class="flex flex-wrap items-center justify-between gap-3 px-3 py-2.5 border-t border-[var(--border)] bg-[var(--bg2)]">
            <p class="text-[12px] text-[var(--text2)]">
                Showing
                <span class="font-medium text-[var(--text)]">{{ $rows->firstItem() }}–{{ $rows->lastItem() }}</span>
                of
                <span class="font-medium text-[var(--text)]">{{ $rows->total() }}</span>
                results
                @if(count($selected) > 0)
                    &nbsp;·&nbsp;
                    <span class="text-[var(--accent-text)] font-medium">{{ count($selected) }} selected</span>
                @endif
            </p>

            {{-- Pagination links --}}
            <nav class="flex items-center gap-1" aria-label="Pagination">
                {{-- Previous --}}
                <button
                    wire:click="previousPage"
                    @disabled(!$rows->onFirstPage())
                    class="w-8 h-8 flex items-center justify-center rounded-[var(--radius)] border border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] text-[13px] font-medium transition-colors hover:bg-[var(--bg2)] disabled:opacity-40 disabled:cursor-not-allowed"
                    aria-label="Previous page"
                >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                </button>

                {{-- Page numbers --}}
                @foreach($rows->getUrlRange(max(1, $rows->currentPage() - 2), min($rows->lastPage(), $rows->currentPage() + 2)) as $page => $url)
                    <button
                        wire:click="gotoPage({{ $page }})"
                        class="w-8 h-8 flex items-center justify-center rounded-[var(--radius)] border text-[13px] font-medium transition-colors
                            {{ $page === $rows->currentPage()
                                ? 'bg-[var(--accent)] text-white border-[var(--accent)]'
                                : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)]' }}"
                        aria-current="{{ $page === $rows->currentPage() ? 'page' : 'false' }}"
                        aria-label="Page {{ $page }}"
                    >
                        {{ $page }}
                    </button>
                @endforeach

                {{-- Next --}}
                <button
                    wire:click="nextPage"
                    @disabled($rows->onLastPage())
                    class="w-8 h-8 flex items-center justify-center rounded-[var(--radius)] border border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] text-[13px] font-medium transition-colors hover:bg-[var(--bg2)] disabled:opacity-40 disabled:cursor-not-allowed"
                    aria-label="Next page"
                >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                </button>
            </nav>
        </div>
    @endif
</div>
