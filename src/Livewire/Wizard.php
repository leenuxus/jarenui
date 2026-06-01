<?php

namespace JarenUI\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * JarenUI Wizard — multi-step form component.
 *
 * Extend this class in your application:
 *
 *   class OnboardingWizard extends \JarenUI\Livewire\Wizard
 *   {
 *       public array $steps = [
 *           ['id' => 'account',  'label' => 'Account',  'icon' => 'user'],
 *           ['id' => 'plan',     'label' => 'Plan',     'icon' => 'credit-card'],
 *           ['id' => 'review',   'label' => 'Review',   'icon' => 'clipboard-check'],
 *       ];
 *
 *       // One property bag per step (optional but recommended)
 *       public array $account = ['first_name' => '', 'email' => ''];
 *       public array $plan    = ['plan_id' => null];
 *
 *       // Validation rules — keyed by step id
 *       protected array $stepRules = [
 *           'account' => [
 *               'account.first_name' => 'required|string|max:100',
 *               'account.email'      => 'required|email',
 *           ],
 *           'plan' => [
 *               'plan.plan_id' => 'required',
 *           ],
 *       ];
 *
 *       // Called on the final Next press
 *       public function submit(): void
 *       {
 *           User::create($this->account);
 *           $this->complete();           // advances to "done" state
 *       }
 *   }
 *
 * In Blade:
 *   <livewire:onboarding-wizard/>
 *
 * Listen to events:
 *   Livewire::on('jaren-wizard-step-changed', fn($step, $index) => ...)
 *   Livewire::on('jaren-wizard-completed',    fn($data)         => ...)
 *   Livewire::on('jaren-wizard-cancelled',    fn()              => ...)
 */
abstract class Wizard extends Component
{
    // ── Configuration (override in subclass) ──────────────────────────────────

    /**
     * Step definitions.
     *
     * Each entry: ['id' => string, 'label' => string, 'icon' => string (optional)]
     *
     * @var array<int, array{id:string, label:string, icon?:string}>
     */
    public array $steps = [];

    /** Show step icons instead of numbers when an 'icon' key is present. */
    public bool $showIcons = false;

    /** Allow clicking a completed step to jump back to it. */
    public bool $clickable = true;

    /** Show a linear progress bar beneath the stepper. */
    public bool $showProgress = false;

    /** Variant of the stepper bar: 'default' | 'numbered' | 'minimal' */
    public string $variant = 'default';

    /** Size of the component: 'sm' | 'md' | 'lg' */
    public string $size = 'md';

    // ── State ─────────────────────────────────────────────────────────────────

    /** Zero-based index of the current step. */
    public int $currentStep = 0;

    /** Whether the wizard has been completed. */
    public bool $completed = false;

    /** Whether the wizard has been cancelled. */
    public bool $cancelled = false;

    /**
     * Validation rule sets keyed by step id.
     *
     * @var array<string, array<string, string>>
     */
    protected array $stepRules = [];

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function currentStepId(): string
    {
        return $this->steps[$this->currentStep]['id'] ?? (string) $this->currentStep;
    }

    #[Computed]
    public function currentStepLabel(): string
    {
        return $this->steps[$this->currentStep]['label'] ?? '';
    }

    #[Computed]
    public function isFirstStep(): bool
    {
        return $this->currentStep === 0;
    }

    #[Computed]
    public function isLastStep(): bool
    {
        return $this->currentStep === count($this->steps) - 1;
    }

    #[Computed]
    public function progressPercent(): int
    {
        $total = max(count($this->steps) - 1, 1);
        return (int) round(($this->currentStep / $total) * 100);
    }

    #[Computed]
    public function stepCount(): int
    {
        return count($this->steps);
    }

    // ── Navigation ────────────────────────────────────────────────────────────

    /**
     * Move to the next step, validating the current step first.
     * On the last step, calls submit().
     */
    public function next(): void
    {
        if (! $this->validateStep($this->currentStepId)) {
            return;
        }

        $this->onStepLeaving($this->currentStepId);

        if ($this->isLastStep) {
            $this->submit();
            return;
        }

        $this->currentStep++;
        $this->onStepEntering($this->currentStepId);

        $this->dispatch('jaren-wizard-step-changed',
            step:  $this->currentStepId,
            index: $this->currentStep,
        );
    }

    /**
     * Move to the previous step.
     */
    public function previous(): void
    {
        if ($this->isFirstStep) {
            return;
        }

        $this->onStepLeaving($this->currentStepId);
        $this->currentStep--;
        $this->onStepEntering($this->currentStepId);

        $this->dispatch('jaren-wizard-step-changed',
            step:  $this->currentStepId,
            index: $this->currentStep,
        );
    }

    /**
     * Jump to a specific step by index (only allowed for completed steps).
     */
    public function goToStep(int $index): void
    {
        if (! $this->clickable) {
            return;
        }

        if ($index < 0 || $index >= count($this->steps)) {
            return;
        }

        // Can only jump back (not forward) unless step is already done
        if ($index > $this->currentStep) {
            return;
        }

        $this->onStepLeaving($this->currentStepId);
        $this->currentStep = $index;
        $this->onStepEntering($this->currentStepId);

        $this->dispatch('jaren-wizard-step-changed',
            step:  $this->currentStepId,
            index: $this->currentStep,
        );
    }

    /**
     * Cancel the wizard.
     */
    public function cancel(): void
    {
        $this->cancelled = true;
        $this->onCancel();
        $this->dispatch('jaren-wizard-cancelled');
    }

    /**
     * Mark the wizard as complete (call this from submit() when done).
     */
    public function complete(): void
    {
        $this->completed = true;
        $this->dispatch('jaren-wizard-completed', data: $this->completedData());
    }

    // ── Hooks (override in subclass) ──────────────────────────────────────────

    /**
     * Called when the wizard is submitted (last step's Next pressed).
     * Override to persist data, fire jobs, etc.
     */
    public function submit(): void
    {
        $this->complete();
    }

    /**
     * Return data attached to the jaren-wizard-completed event.
     * Override to return your collected form data.
     */
    protected function completedData(): array
    {
        return [];
    }

    /**
     * Called just before leaving a step. Override to perform cleanup.
     */
    protected function onStepLeaving(string $stepId): void {}

    /**
     * Called just after entering a step. Override to load data, reset state, etc.
     */
    protected function onStepEntering(string $stepId): void {}

    /**
     * Called when the wizard is cancelled.
     */
    protected function onCancel(): void {}

    // ── Validation ────────────────────────────────────────────────────────────

    /**
     * Validate the given step.
     * Runs $stepRules[$stepId] if defined, or returns true if none.
     *
     * Override for custom per-step validation logic.
     */
    protected function validateStep(string $stepId): bool
    {
        if (! array_key_exists($stepId, $this->stepRules)) {
            return true;
        }

        $this->validate($this->stepRules[$stepId]);
        return true;
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('jarenui::components.jaren.wizard');
    }
}
