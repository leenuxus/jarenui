<?php

namespace JarenUI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeWizardCommand extends Command
{
    protected $signature = 'jaren:make-wizard
                            {name         : Class name, e.g. OnboardingWizard}
                            {--steps=     : Comma-separated step ids, e.g. account,plan,review}
                            {--variant=   : Stepper variant: default|numbered|minimal (default: default)}
                            {--size=      : Size: sm|md|lg (default: md)}
                            {--force      : Overwrite existing file}';

    protected $description = 'Generate a new JarenUI Wizard Livewire component';

    public function handle(): int
    {
        $name    = $this->argument('name');
        $class   = Str::studly($name);
        $path    = app_path("Livewire/{$class}.php");
        $viewDir = resource_path('views/livewire/' . Str::kebab($class));

        if (File::exists($path) && ! $this->option('force')) {
            $this->components->error("File [{$path}] already exists. Use --force to overwrite.");
            return self::FAILURE;
        }

        // Parse steps
        $stepInput = $this->option('steps') ?: 'details,confirm';
        $stepIds   = array_map('trim', explode(',', $stepInput));
        $steps     = array_map(fn ($id) => [
            'id'    => $id,
            'label' => Str::title(str_replace(['-', '_'], ' ', $id)),
        ], $stepIds);

        $variant = $this->option('variant') ?: 'default';
        $size    = $this->option('size')    ?: 'md';

        // Create the PHP class
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->buildClass($class, $steps, $variant, $size));

        // Create per-step Blade partials
        File::ensureDirectoryExists($viewDir);
        foreach ($steps as $step) {
            $partialPath = $viewDir . '/' . $step['id'] . '.blade.php';
            if (! File::exists($partialPath)) {
                File::put($partialPath, $this->buildStepPartial($step, $class));
            }
        }

        $this->components->info("Wizard created: [app/Livewire/{$class}.php]");

        $this->line('');
        $this->line('  <fg=green>Steps:</> ' . implode(' → ', array_column($steps, 'label')));
        $this->line('  <fg=green>Partials:</> resources/views/livewire/' . Str::kebab($class) . '/*.blade.php');
        $this->line('  <fg=green>Usage:</>  <fg=cyan><livewire:' . $this->alias($class) . '/>');
        $this->line('');
        $this->line('  Next steps:');
        $this->line('  1. Add validation rules to <fg=cyan>$stepRules</> in the class');
        $this->line('  2. Fill in each step partial in <fg=cyan>resources/views/livewire/' . Str::kebab($class) . '/');
        $this->line('  3. Implement <fg=cyan>submit()</> to persist data');
        $this->line('');

        return self::SUCCESS;
    }

    protected function buildClass(string $class, array $steps, string $variant, string $size): string
    {
        $stepsArray = json_encode(
            array_map(fn ($s) => ['id' => $s['id'], 'label' => $s['label']], $steps),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
        $stepsArray = '        ' . str_replace("\n", "\n        ", $stepsArray);

        $properties = implode("\n\n", array_map(fn ($s) => <<<PHP
    /** Data collected on the '{$s['id']}' step. */
    public array \${$s['id']} = [];
PHP, $steps));

        $stepRules = implode("\n", array_map(fn ($s) => <<<PHP
            '{$s['id']}' => [
                // '{$s['id']}.field_name' => 'required|string|max:255',
            ],
PHP, $steps));

        $renderMethods = implode("\n\n", array_map(fn ($s) => <<<PHP
    public function render{$s['label']}(): string
    {
        return view('livewire.{$this->alias($class)}.{$s['id']}', [
            'data' => \$this->{$s['id']},
        ])->render();
    }
PHP, array_map(fn ($s) => array_merge($s, ['label' => Str::studly($s['id'])]), $steps)));

        $submitBody = implode("\n        ", array_map(
            fn ($s) => "// \$this->{$s['id']} — data from '{$s['id']}' step",
            $steps
        ));

        return <<<PHP
<?php

namespace App\Livewire;

use JarenUI\Livewire\Wizard;

class {$class} extends Wizard
{
    // ── Steps ──────────────────────────────────────────────────────────────────

    public array \$steps = {$stepsArray};

    public string \$variant = '{$variant}';
    public string \$size    = '{$size}';

    // ── Data properties (one per step) ─────────────────────────────────────────

{$properties}

    // ── Validation (per step) ──────────────────────────────────────────────────

    protected array \$stepRules = [
{$stepRules}
    ];

    // ── Step content renderers ─────────────────────────────────────────────────
    //
    // Each method renders the Blade partial for its step.
    // Alternatively, return a Livewire view and use named slots.

{$renderMethods}

    // ── Submit ─────────────────────────────────────────────────────────────────

    public function submit(): void
    {
        {$submitBody}

        // Persist your data here, e.g.:
        // User::create(array_merge(\$this->details, \$this->plan));

        \$this->complete();
    }

    protected function completedData(): array
    {
        return [
PHP . implode(",\n            ", array_map(fn ($s) => "'{$s['id']}' => \$this->{$s['id']}", $steps)) . <<<PHP

        ];
    }
}
PHP;
    }

    protected function buildStepPartial(array $step, string $class): string
    {
        $label = Str::title(str_replace(['-', '_'], ' ', $step['id']));

        return <<<BLADE
{{--
  Wizard step: {$label}
  Class: App\Livewire\\{$class}
--}}

<div>
    <h3 class="text-[15px] font-medium text-[var(--text)] mb-1">{$label}</h3>
    <p class="text-[13px] text-[var(--text2)] mb-5 leading-relaxed">
        Complete the {$label} step.
    </p>

    {{-- Add your form fields here, e.g.: --}}
    {{--
    <x-jaren::field label="Name" :error="\$errors->first('{$step['id']}.name')">
        <x-jaren::input wire:model="{$step['id']}.name" placeholder="Enter name"/>
    </x-jaren::field>
    --}}

    <div class="p-4 bg-[var(--bg2)] rounded-[var(--radius-lg)] text-[12px] text-[var(--text3)] border border-dashed border-[var(--border2)]">
        <strong class="font-medium text-[var(--text2)]">Step: {$label}</strong><br>
        Add your fields to <code>resources/views/livewire/{$this->alias($class)}/{$step['id']}.blade.php</code>
    </div>
</div>
BLADE;
    }

    protected function alias(string $class): string
    {
        return Str::of($class)->kebab()->prepend('jaren.')->toString();
    }
}
