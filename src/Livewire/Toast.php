<?php

namespace JarenUI\Livewire;

use Livewire\Component;

/**
 * JarenUI Toast — Livewire notification system
 *
 * Dispatch from any Livewire component:
 *   $this->dispatch('jaren-toast', type: 'success', title: 'Saved!', message: 'Changes applied.');
 *   $this->dispatch('jaren-toast', type: 'danger',  title: 'Error',  message: 'Something went wrong.', duration: 0); // 0 = persistent
 *
 * Dispatch from JavaScript / Alpine:
 *   $dispatch('jaren-toast', { type: 'info', title: 'Hello', message: 'World', duration: 4000 })
 *
 * Supported types: success | danger | warning | info
 *
 * Include once in your layout (e.g. app.blade.php):
 *   <livewire:jaren.toast />
 */
class Toast extends Component
{
    /** @var array<int, array> */
    public array $toasts = [];

    private int $nextId = 0;

    protected $listeners = ['jaren-toast' => 'addToast'];

    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Add a toast notification.
     *
     * @param string      $type     success | danger | warning | info
     * @param string      $title
     * @param string|null $message
     * @param int         $duration ms before auto-dismiss (0 = persistent)
     * @param string|null $action   Optional action label
     * @param string|null $actionEvent Livewire event to dispatch on action click
     */
    public function addToast(
        string  $type         = 'info',
        string  $title        = '',
        ?string $message      = null,
        int     $duration     = 4000,
        ?string $action       = null,
        ?string $actionEvent  = null,
    ): void {
        $id = ++$this->nextId;

        $this->toasts[] = [
            'id'          => $id,
            'type'        => in_array($type, ['success','danger','warning','info']) ? $type : 'info',
            'title'       => $title,
            'message'     => $message,
            'duration'    => $duration,
            'action'      => $action,
            'actionEvent' => $actionEvent,
            'visible'     => true,
        ];
    }

    public function dismiss(int $id): void
    {
        $this->toasts = array_filter(
            $this->toasts,
            fn($t) => $t['id'] !== $id
        );
        $this->toasts = array_values($this->toasts);
    }

    public function handleAction(int $id, string $event): void
    {
        $this->dispatch($event, toastId: $id);
        $this->dismiss($id);
    }

    public function render()
    {
        return view('jarenui::components.jaren.toast');
    }
}
