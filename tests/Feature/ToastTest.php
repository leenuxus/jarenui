<?php

use JarenUI\Livewire\Toast;
use Livewire\Livewire;

// ── Toast Livewire component ───────────────────────────────────────────────

test('Toast renders without errors', function () {
    Livewire::test(Toast::class)
        ->assertStatus(200);
});

test('Toast can receive and store a notification', function () {
    Livewire::test(Toast::class)
        ->dispatch('jaren-toast', type: 'success', title: 'Saved!', message: 'All good.')
        ->assertSet('toasts.0.type', 'success')
        ->assertSet('toasts.0.title', 'Saved!')
        ->assertSet('toasts.0.message', 'All good.');
});

test('Toast defaults unknown type to info', function () {
    Livewire::test(Toast::class)
        ->dispatch('jaren-toast', type: 'unknown', title: 'Hello')
        ->assertSet('toasts.0.type', 'info');
});

test('Toast can be dismissed by id', function () {
    $component = Livewire::test(Toast::class)
        ->dispatch('jaren-toast', type: 'info', title: 'Test');

    $id = $component->get('toasts')[0]['id'];

    $component
        ->call('dismiss', $id)
        ->assertCount('toasts', 0);
});

test('Toast stacks multiple notifications', function () {
    Livewire::test(Toast::class)
        ->dispatch('jaren-toast', type: 'success', title: 'First')
        ->dispatch('jaren-toast', type: 'danger',  title: 'Second')
        ->dispatch('jaren-toast', type: 'warning', title: 'Third')
        ->assertCount('toasts', 3);
});

test('Toast action dispatches event and dismisses', function () {
    $component = Livewire::test(Toast::class)
        ->dispatch('jaren-toast', type: 'info', title: 'Deleted', action: 'Undo', actionEvent: 'undo-delete');

    $id = $component->get('toasts')[0]['id'];

    $component
        ->call('handleAction', $id, 'undo-delete')
        ->assertDispatched('undo-delete')
        ->assertCount('toasts', 0);
});

// ── HasToast trait ─────────────────────────────────────────────────────────

test('HasToast trait dispatches toast from component', function () {
    $component = new class extends \Livewire\Component {
        use \JarenUI\Concerns\HasToast;

        public function doSomething(): void
        {
            $this->toastSuccess('Done!', 'It worked.');
        }

        public function render() { return '<div></div>'; }
    };

    Livewire::test($component::class)
        ->call('doSomething')
        ->assertDispatched('jaren-toast');
});
