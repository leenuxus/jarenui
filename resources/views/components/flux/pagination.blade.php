{{--
  Usage (with Livewire paginator):
    <x-jaren::pagination :paginator="$users"/>

  Usage (manual):
    <x-jaren::pagination
        :current="3"
        :total="25"
        :per-page="10"
        wire:click.prevent="gotoPage($event.target.dataset.page)"
    />
--}}

@props([
    'paginator'   => null,    // LengthAwarePaginator instance
    'current'     => null,    // manual: current page number
    'total'       => null,    // manual: total items
    'perPage'     => null,    // manual: items per page
    'window'      => 2,       // pages to show either side of current
    'showSummary' => true,    // "Showing X-Y of Z"
    'simple'      => false,   // just prev/next, no numbers
    'size'        => 'md',    // sm | md
])

@php
// Resolve from paginator or manual props
if ($paginator) {
    $currentPage = $paginator->currentPage();
    $lastPage    = $paginator->lastPage();
    $from        = $paginator->firstItem() ?? 0;
    $to          = $paginator->lastItem()  ?? 0;
    $totalItems  = $paginator->total();
    $onFirst     = $paginator->onFirstPage();
    $onLast      = !$paginator->hasMorePages();
} else {
    $totalItems  = (int) $total;
    $perPageNum  = (int) ($perPage ?? 10);
    $currentPage = (int) ($current ?? 1);
    $lastPage    = max(1, (int) ceil($totalItems / $perPageNum));
    $from        = (($currentPage - 1) * $perPageNum) + 1;
    $to          = min($currentPage * $perPageNum, $totalItems);
    $onFirst     = $currentPage <= 1;
    $onLast      = $currentPage >= $lastPage;
}

// Build page window: [1 ... 3 4 [5] 6 7 ... 25]
$pages = [];
if (!$simple) {
    $start = max(2, $currentPage - $window);
    $end   = min($lastPage - 1, $currentPage + $window);
    $pages = range($start, $end);
}

$btnBase = 'inline-flex items-center justify-center rounded-[var(--radius)] border font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)] focus-visible:ring-offset-1';
$sizes = [
    'sm' => 'w-7 h-7 text-[12px]',
    'md' => 'w-8 h-8 text-[13px]',
];
$sz = $sizes[$size] ?? $sizes['md'];
@endphp

<nav
    {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-3']) }}
    aria-label="Pagination"
>
    {{-- Summary --}}
    @if($showSummary && $totalItems > 0)
        <p class="text-[12px] text-[var(--text2)] mr-auto" aria-live="polite">
            Showing
            <span class="font-medium text-[var(--text)]">{{ number_format($from) }}–{{ number_format($to) }}</span>
            of
            <span class="font-medium text-[var(--text)]">{{ number_format($totalItems) }}</span>
        </p>
    @endif

    <div class="flex items-center gap-1" role="list">

        {{-- Previous --}}
        @if($paginator)
            <a
                href="{{ $onFirst ? '#' : $paginator->previousPageUrl() }}"
                class="{{ $btnBase }} {{ $sz }} {{ $onFirst ? 'opacity-40 pointer-events-none' : '' }} border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)] hover:text-[var(--text)]"
                aria-label="Previous page"
                @if($onFirst) aria-disabled="true" @endif
            >
                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 010 1.06L8.06 10l3.72 3.72a.75.75 0 11-1.06 1.06l-4.25-4.25a.75.75 0 010-1.06l4.25-4.25a.75.75 0 011.06 0z" clip-rule="evenodd"/></svg>
            </a>
        @else
            <button
                type="button"
                @disabled($onFirst)
                wire:click="previousPage"
                class="{{ $btnBase }} {{ $sz }} border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)] hover:text-[var(--text)] disabled:opacity-40 disabled:cursor-not-allowed"
                aria-label="Previous page"
            >
                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 010 1.06L8.06 10l3.72 3.72a.75.75 0 11-1.06 1.06l-4.25-4.25a.75.75 0 010-1.06l4.25-4.25a.75.75 0 011.06 0z" clip-rule="evenodd"/></svg>
            </button>
        @endif

        @if(!$simple)
            {{-- First page --}}
            @if($paginator)
                <a href="{{ $paginator->url(1) }}" class="{{ $btnBase }} {{ $sz }} {{ $currentPage === 1 ? 'bg-[var(--accent)] text-white border-[var(--accent)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)]' }}" aria-label="Page 1" @if($currentPage===1) aria-current="page" @endif>1</a>
            @else
                <button type="button" wire:click="gotoPage(1)" class="{{ $btnBase }} {{ $sz }} {{ $currentPage === 1 ? 'bg-[var(--accent)] text-white border-[var(--accent)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)]' }}" @if($currentPage===1) aria-current="page" @endif>1</button>
            @endif

            {{-- Left ellipsis --}}
            @if($start > 2)
                <span class="{{ $btnBase }} {{ $sz }} border-transparent text-[var(--text3)] cursor-default pointer-events-none">…</span>
            @endif

            {{-- Window --}}
            @foreach($pages as $page)
                @if($paginator)
                    <a href="{{ $paginator->url($page) }}" class="{{ $btnBase }} {{ $sz }} {{ $currentPage === $page ? 'bg-[var(--accent)] text-white border-[var(--accent)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)]' }}" aria-label="Page {{ $page }}" @if($currentPage===$page) aria-current="page" @endif>{{ $page }}</a>
                @else
                    <button type="button" wire:click="gotoPage({{ $page }})" class="{{ $btnBase }} {{ $sz }} {{ $currentPage === $page ? 'bg-[var(--accent)] text-white border-[var(--accent)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)]' }}" @if($currentPage===$page) aria-current="page" @endif>{{ $page }}</button>
                @endif
            @endforeach

            {{-- Right ellipsis --}}
            @if($end < $lastPage - 1)
                <span class="{{ $btnBase }} {{ $sz }} border-transparent text-[var(--text3)] cursor-default pointer-events-none">…</span>
            @endif

            {{-- Last page --}}
            @if($lastPage > 1)
                @if($paginator)
                    <a href="{{ $paginator->url($lastPage) }}" class="{{ $btnBase }} {{ $sz }} {{ $currentPage === $lastPage ? 'bg-[var(--accent)] text-white border-[var(--accent)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)]' }}" aria-label="Page {{ $lastPage }}" @if($currentPage===$lastPage) aria-current="page" @endif>{{ $lastPage }}</a>
                @else
                    <button type="button" wire:click="gotoPage({{ $lastPage }})" class="{{ $btnBase }} {{ $sz }} {{ $currentPage === $lastPage ? 'bg-[var(--accent)] text-white border-[var(--accent)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)]' }}" @if($currentPage===$lastPage) aria-current="page" @endif>{{ $lastPage }}</button>
                @endif
            @endif
        @endif

        {{-- Next --}}
        @if($paginator)
            <a
                href="{{ $onLast ? '#' : $paginator->nextPageUrl() }}"
                class="{{ $btnBase }} {{ $sz }} {{ $onLast ? 'opacity-40 pointer-events-none' : '' }} border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)] hover:text-[var(--text)]"
                aria-label="Next page"
                @if($onLast) aria-disabled="true" @endif
            >
                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd"/></svg>
            </a>
        @else
            <button
                type="button"
                @disabled($onLast)
                wire:click="nextPage"
                class="{{ $btnBase }} {{ $sz }} border-[var(--border)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg2)] hover:text-[var(--text)] disabled:opacity-40 disabled:cursor-not-allowed"
                aria-label="Next page"
            >
                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd"/></svg>
            </button>
        @endif
    </div>
</nav>
