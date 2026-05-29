<?php

namespace JarenUI\Concerns;

/**
 * HasToast — convenience methods for dispatching JarenUI toast notifications
 * from any Livewire component.
 *
 * Usage:
 *   class MyComponent extends Component
 *   {
 *       use \JarenUI\Concerns\HasToast;
 *
 *       public function save(): void
 *       {
 *           // …
 *           $this->toast()->success('Saved!', 'Your changes have been applied.');
 *       }
 *   }
 */
trait HasToast
{
    /**
     * Get a fluent Toast builder scoped to this component.
     */
    protected function toast(): ToastBuilder
    {
        return new ToastBuilder($this);
    }

    // ── Shorthand methods ──────────────────────────────────────────────────────

    protected function toastSuccess(string $title, ?string $message = null, int $duration = 4000): void
    {
        $this->dispatchJarenToast('success', $title, $message, $duration);
    }

    protected function toastDanger(string $title, ?string $message = null, int $duration = 0): void
    {
        $this->dispatchJarenToast('danger', $title, $message, $duration);
    }

    protected function toastWarning(string $title, ?string $message = null, int $duration = 5000): void
    {
        $this->dispatchJarenToast('warning', $title, $message, $duration);
    }

    protected function toastInfo(string $title, ?string $message = null, int $duration = 4000): void
    {
        $this->dispatchJarenToast('info', $title, $message, $duration);
    }

    protected function dispatchJarenToast(
        string  $type,
        string  $title,
        ?string $message   = null,
        int     $duration  = 4000,
        ?string $action    = null,
        ?string $actionEvent = null,
    ): void {
        $this->dispatch('jaren-toast',
            type:        $type,
            title:       $title,
            message:     $message,
            duration:    $duration,
            action:      $action,
            actionEvent: $actionEvent,
        );
    }
}


/**
 * Fluent builder returned by HasToast::toast().
 * Allows chaining: $this->toast()->duration(0)->action('Undo', 'undo')->success('Deleted');
 */
class ToastBuilder
{
    protected int     $duration    = 4000;
    protected ?string $action      = null;
    protected ?string $actionEvent = null;

    public function __construct(protected $component) {}

    public function duration(int $ms): static
    {
        $this->duration = $ms;
        return $this;
    }

    public function persistent(): static
    {
        return $this->duration(0);
    }

    public function action(string $label, string $event): static
    {
        $this->action      = $label;
        $this->actionEvent = $event;
        return $this;
    }

    public function success(string $title, ?string $message = null): void
    {
        $this->send('success', $title, $message);
    }

    public function danger(string $title, ?string $message = null): void
    {
        $this->send('danger', $title, $message);
    }

    public function warning(string $title, ?string $message = null): void
    {
        $this->send('warning', $title, $message);
    }

    public function info(string $title, ?string $message = null): void
    {
        $this->send('info', $title, $message);
    }

    protected function send(string $type, string $title, ?string $message): void
    {
        $this->component->dispatch('jaren-toast',
            type:        $type,
            title:       $title,
            message:     $message,
            duration:    $this->duration,
            action:      $this->action,
            actionEvent: $this->actionEvent,
        );
    }
}
