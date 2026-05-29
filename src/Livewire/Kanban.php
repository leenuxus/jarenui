<?php

namespace JarenUI\Livewire;

use Livewire\Component;

/**
 * JarenUI Kanban Board
 *
 * Usage in Blade:
 *   <livewire:jaren.kanban :columns="$columns" :cards="$cards" />
 *
 * $columns = [
 *   ['id' => 'backlog',     'label' => 'Backlog',     'color' => '#9a9894', 'limit' => null],
 *   ['id' => 'in_progress', 'label' => 'In Progress', 'color' => '#d97706', 'limit' => 3],
 *   ['id' => 'review',      'label' => 'Review',      'color' => '#2563eb', 'limit' => null],
 *   ['id' => 'done',        'label' => 'Done',         'color' => '#16a34a', 'limit' => null],
 * ];
 *
 * $cards = [
 *   ['id' => 1, 'column' => 'backlog',     'title' => 'Fix login bug',  'tag' => 'Bug',     'tag_color' => 'red',   'assignee' => 'JD', 'assignee_color' => '#2563eb', 'due' => '2026-06-01', 'comments' => 3],
 *   ['id' => 2, 'column' => 'in_progress', 'title' => 'New dashboard',  'tag' => 'Feature', 'tag_color' => 'blue',  'assignee' => 'AK', 'assignee_color' => '#ea580c'],
 * ];
 */
class Kanban extends Component
{
    // ── Props ──────────────────────────────────────────────────────────────────

    /** @var array<int, array{id: string, label: string, color: string, limit: int|null}> */
    public array $columns = [];

    /** @var array<int, array> */
    public array $cards = [];

    // ── State ──────────────────────────────────────────────────────────────────

    public ?int   $editingCardId  = null;
    public ?string $addingToColumn = null;
    public string  $newCardTitle   = '';

    // ── Mount ──────────────────────────────────────────────────────────────────

    public function mount(array $columns = [], array $cards = []): void
    {
        $this->columns = $columns;
        $this->cards   = $cards;
    }

    // ── Card CRUD ──────────────────────────────────────────────────────────────

    public function addCard(): void
    {
        $this->validate(['newCardTitle' => 'required|string|max:255']);

        $id = max(array_column($this->cards, 'id') ?: [0]) + 1;

        $this->cards[] = [
            'id'     => $id,
            'column' => $this->addingToColumn,
            'title'  => trim($this->newCardTitle),
        ];

        $this->reset('newCardTitle', 'addingToColumn');
        $this->dispatch('jaren-kanban-card-added', cardId: $id, column: $this->addingToColumn);
    }

    public function cancelAdd(): void
    {
        $this->reset('newCardTitle', 'addingToColumn');
    }

    public function deleteCard(int $id): void
    {
        $this->cards = array_values(array_filter($this->cards, fn ($c) => $c['id'] !== $id));
        $this->dispatch('jaren-kanban-card-deleted', cardId: $id);
    }

    // ── Drag & Drop ────────────────────────────────────────────────────────────

    /**
     * Called by Alpine when a card is dropped onto a column.
     * Payload from JS: { cardId: int, targetColumn: string, afterCardId: int|null }
     */
    public function moveCard(int $cardId, string $targetColumn, ?int $afterCardId = null): void
    {
        $fromColumn = null;

        // Update the card's column
        foreach ($this->cards as &$card) {
            if ($card['id'] === $cardId) {
                $fromColumn    = $card['column'];
                $card['column'] = $targetColumn;
                break;
            }
        }
        unset($card);

        // Re-order: remove card, re-insert after $afterCardId within column
        if ($afterCardId !== null) {
            $moving    = null;
            $remaining = [];

            foreach ($this->cards as $c) {
                if ($c['id'] === $cardId) { $moving = $c; continue; }
                $remaining[] = $c;
            }

            $reordered = [];
            foreach ($remaining as $c) {
                $reordered[] = $c;
                if ($c['id'] === $afterCardId && $c['column'] === $targetColumn) {
                    $reordered[] = $moving;
                }
            }
            $this->cards = $reordered;
        }

        $this->dispatch('jaren-kanban-card-moved', [
            'cardId'       => $cardId,
            'fromColumn'   => $fromColumn,
            'targetColumn' => $targetColumn,
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function cardsForColumn(string $columnId): array
    {
        return array_values(array_filter($this->cards, fn ($c) => $c['column'] === $columnId));
    }

    // ── Render ─────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('jarenui::components.jaren.kanban');
    }
}
