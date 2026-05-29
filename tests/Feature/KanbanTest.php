<?php

use JarenUI\Livewire\Kanban;
use Livewire\Livewire;

// ── Shared fixtures ────────────────────────────────────────────────────────

function kanbanColumns(): array
{
    return [
        ['id' => 'todo',        'label' => 'To Do',       'color' => '#9a9894'],
        ['id' => 'in_progress', 'label' => 'In Progress', 'color' => '#d97706', 'limit' => 2],
        ['id' => 'done',        'label' => 'Done',        'color' => '#16a34a'],
    ];
}

function kanbanCards(): array
{
    return [
        ['id' => 1, 'column' => 'todo',        'title' => 'Card A', 'tag' => 'Bug',     'tag_color' => 'red'],
        ['id' => 2, 'column' => 'todo',        'title' => 'Card B', 'tag' => 'Feature', 'tag_color' => 'blue'],
        ['id' => 3, 'column' => 'in_progress', 'title' => 'Card C'],
        ['id' => 4, 'column' => 'done',        'title' => 'Card D'],
    ];
}

// ── Tests ──────────────────────────────────────────────────────────────────

test('Kanban renders with columns and cards', function () {
    Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ])
        ->assertStatus(200)
        ->assertSee('To Do')
        ->assertSee('In Progress')
        ->assertSee('Done')
        ->assertSee('Card A')
        ->assertSee('Card C');
});

test('Kanban cardsForColumn returns correct cards', function () {
    $component = Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ]);

    $kanban = $component->instance();
    $todoCards = $kanban->cardsForColumn('todo');

    expect($todoCards)->toHaveCount(2);
    expect($todoCards[0]['title'])->toBe('Card A');
    expect($todoCards[1]['title'])->toBe('Card B');
});

test('Kanban moves card to new column', function () {
    Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ])
        ->call('moveCard', 1, 'in_progress', null)
        ->assertDispatched('flux-kanban-card-moved')
        ->tap(function ($component) {
            $cards = $component->get('cards');
            $moved = collect($cards)->firstWhere('id', 1);
            expect($moved['column'])->toBe('in_progress');
        });
});

test('Kanban dispatches moved event with correct payload', function () {
    Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ])
        ->call('moveCard', 2, 'done', null)
        ->assertDispatched('flux-kanban-card-moved', fn ($event) =>
            ($event['cardId'] ?? $event[0]['cardId'] ?? null) == 2
        );
});

test('Kanban adds a new card', function () {
    Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ])
        ->set('addingToColumn', 'todo')
        ->set('newCardTitle', 'Brand new card')
        ->call('addCard')
        ->assertDispatched('flux-kanban-card-added')
        ->tap(function ($component) {
            $cards = $component->get('cards');
            $new   = collect($cards)->firstWhere('title', 'Brand new card');
            expect($new)->not->toBeNull();
            expect($new['column'])->toBe('todo');
        });
});

test('Kanban addCard resets state after adding', function () {
    Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ])
        ->set('addingToColumn', 'todo')
        ->set('newCardTitle', 'My new card')
        ->call('addCard')
        ->assertSet('newCardTitle', '')
        ->assertSet('addingToColumn', null);
});

test('Kanban requires a title to add card', function () {
    Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ])
        ->set('addingToColumn', 'todo')
        ->set('newCardTitle', '')
        ->call('addCard')
        ->assertHasErrors(['newCardTitle' => 'required']);
});

test('Kanban cancel add resets state', function () {
    Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ])
        ->set('addingToColumn', 'todo')
        ->set('newCardTitle', 'Partial input')
        ->call('cancelAdd')
        ->assertSet('newCardTitle', '')
        ->assertSet('addingToColumn', null);
});

test('Kanban deletes a card', function () {
    Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ])
        ->call('deleteCard', 1)
        ->assertDispatched('flux-kanban-card-deleted')
        ->tap(function ($component) {
            $cards = $component->get('cards');
            expect(collect($cards)->pluck('id'))->not->toContain(1);
        });
});

test('Kanban delete dispatches correct card id', function () {
    Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ])
        ->call('deleteCard', 3)
        ->assertDispatched('flux-kanban-card-deleted', fn ($e) =>
            ($e['cardId'] ?? $e[0]['cardId'] ?? null) == 3
        );
});

test('Kanban card count is correct after operations', function () {
    $component = Livewire::test(Kanban::class, [
        'columns' => kanbanColumns(),
        'cards'   => kanbanCards(),
    ]);

    expect($component->get('cards'))->toHaveCount(4);

    $component
        ->set('addingToColumn', 'todo')
        ->set('newCardTitle', 'New')
        ->call('addCard');

    expect($component->get('cards'))->toHaveCount(5);

    $component->call('deleteCard', 1);

    expect($component->get('cards'))->toHaveCount(4);
});
