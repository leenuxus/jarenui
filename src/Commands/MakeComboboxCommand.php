<?php

namespace JarenUI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeComboboxCommand extends Command
{
    protected $signature = 'jaren:make-combobox
                            {name           : Class name, e.g. UserCombobox}
                            {--model=       : Eloquent model to search (e.g. User)}
                            {--label=name   : Column used as label}
                            {--value=id     : Column used as value}
                            {--search=name  : Comma-separated searchable columns}
                            {--multiple     : Enable multi-select mode}
                            {--async        : Use Livewire server-side search (default when --model given)}
                            {--force        : Overwrite existing file}';

    protected $description = 'Generate a new JarenUI Combobox component';

    public function handle(): int
    {
        $name     = $this->argument('name');
        $class    = Str::studly($name);
        $model    = $this->option('model');
        $multiple = $this->option('multiple');
        $async    = $this->option('async') || $model;

        $path = $async
            ? app_path("Livewire/{$class}.php")
            : resource_path('views/components/' . Str::kebab($class) . '.blade.php');

        if (File::exists($path) && ! $this->option('force')) {
            $this->components->error("File [{$path}] already exists. Use --force to overwrite.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $async
            ? $this->buildAsyncClass($class, $model, $multiple)
            : $this->buildBladeUsage($class, $multiple)
        );

        if ($async) {
            $this->components->info("AsyncCombobox created: [app/Livewire/{$class}.php]");
            $this->line('');
            $this->line("  Usage: <fg=cyan><livewire:" . $this->alias($class) . " label=\"Search\"/></>");
        } else {
            $this->components->info("Combobox snippet created: [{$path}]");
        }

        $this->line('');
        $this->line('  Override <fg=cyan>search(string \$query): array</> to customise results.');
        $this->line('');

        return self::SUCCESS;
    }

    protected function buildAsyncClass(string $class, ?string $model, bool $multiple): string
    {
        $modelClass  = $model ? Str::studly($model) : 'User';
        $labelCol    = $this->option('label')  ?: 'name';
        $valueCol    = $this->option('value')  ?: 'id';
        $searchCols  = $this->option('search') ?: $labelCol;
        $searchArray = "['" . implode("', '", array_map('trim', explode(',', $searchCols))) . "']";
        $multiProp   = $multiple ? "\n    public bool \$multiple = true;" : '';

        return <<<PHP
<?php

namespace App\Livewire;

use App\Models\\{$modelClass};
use JarenUI\Livewire\AsyncCombobox;

class {$class} extends AsyncCombobox
{
    public string \$label       = 'Search {$modelClass}';
    public string \$placeholder = 'Search…';{$multiProp}

    public function search(string \$query): array
    {
        return {$modelClass}::where(function (\$q) use (\$query) {
                foreach ({$searchArray} as \$col) {
                    \$q->orWhere(\$col, 'like', "%{\$query}%");
                }
            })
            ->limit(\$this->limit)
            ->get()
            ->map(fn (\$row) => [
                'value' => \$row->{$valueCol},
                'label' => \$row->{$labelCol},
                // 'meta'        => \$row->email,
                // 'description' => \$row->role,
                // 'initials'    => strtoupper(substr(\$row->name, 0, 2)),
                // 'color'       => \$row->avatar_color,
            ])
            ->toArray();
    }
}
PHP;
    }

    protected function buildBladeUsage(string $class, bool $multiple): string
    {
        $multi = $multiple ? "\n    multiple" : '';

        return <<<BLADE
{{--
  JarenUI Combobox: {$class}
  Usage: include this snippet where needed, or convert to a Blade component.
--}}

<x-jaren::combobox
    label="Select an option"
    placeholder="Search…"
    wire:model="selectedValue"{$multi}
    :options="[
        // ['value' => 1, 'label' => 'Option A'],
        // ['value' => 2, 'label' => 'Option B', 'meta' => 'hint'],
    ]"
    searchable
    clearable
/>
BLADE;
    }

    protected function alias(string $class): string
    {
        return Str::of($class)->kebab()->prepend('jaren.')->toString();
    }
}
