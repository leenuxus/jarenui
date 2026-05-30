<?php

namespace JarenUI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeEventCalendarCommand extends Command
{
    protected $signature = 'jaren:make-event-calendar
                            {name       : Class name, e.g. MeetingsCalendar}
                            {--model=   : Eloquent model to load events from (e.g. Meeting)}
                            {--force    : Overwrite existing file}';

    protected $description = 'Generate a new JarenUI EventCalendar Livewire component';

    public function handle(): int
    {
        $name  = $this->argument('name');
        $model = $this->option('model');
        $class = Str::studly($name);
        $path  = app_path("Livewire/{$class}.php");

        if (File::exists($path) && ! $this->option('force')) {
            $this->components->error("File [{$path}] already exists. Use --force to overwrite.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->buildClass($class, $model));

        $this->components->info("EventCalendar created: [app/Livewire/{$class}.php]");
        $this->line('');
        $this->line("  Use in Blade: <fg=cyan><livewire:{$this->alias($class)}/></>");
        $this->line('');
        $this->line('  Customise by overriding <fg=cyan>fetchEvents(Carbon \$from, Carbon \$to)</> in the class.');
        $this->line('');

        return self::SUCCESS;
    }

    protected function buildClass(string $class, ?string $model): string
    {
        $modelClass  = $model ? Str::studly($model) : null;
        $modelImport = $modelClass ? "\nuse App\\Models\\{$modelClass};" : '';
        $fetchBody   = $modelClass
            ? $this->fetchWithModel($modelClass)
            : $this->fetchPlaceholder();

        return <<<PHP
<?php

namespace App\Livewire;

use Carbon\Carbon;
use JarenUI\CalendarEvent;
use JarenUI\Livewire\EventCalendar;{$modelImport}

class {$class} extends EventCalendar
{
    /**
     * Views available in the toolbar.
     * Remove any you don't need: 'month', 'week', 'day'
     */
    public array \$availableViews = ['month', 'week', 'day'];

    /**
     * Allow clicking empty dates to create events (emits jaren-event-created).
     */
    public bool \$creatable = true;

    /**
     * First day of the week: 0 = Sunday, 1 = Monday
     */
    public int \$startDay = 0;

    /**
     * Load events from your database for the given date window.
     * Called automatically when the view or period changes.
     *
     * @return CalendarEvent[]
     */
    public function fetchEvents(Carbon \$from, Carbon \$to): array
    {
{$fetchBody}
    }

    /**
     * Handle new event creation (triggered when creatable=true and a date is clicked).
     * Dispatch a modal, redirect, or handle inline.
     */
    public function createEvent(string \$date): void
    {
        // Example:
        // \$this->dispatch('open-modal', name: 'create-event', date: \$date);
        \$this->dispatch('jaren-event-created', date: \$date);
    }
}
PHP;
    }

    protected function fetchWithModel(string $model): string
    {
        $var = lcfirst($model);
        return <<<PHP
        return CalendarEvent::fromCollection(
            {$model}::query()
                ->whereBetween('starts_at', [\$from, \$to])
                ->get(),
            startKey:       'starts_at',
            endKey:         'ends_at',
            titleKey:       'title',
            colorKey:       'color',
            descriptionKey: 'description',
        );
PHP;
    }

    protected function fetchPlaceholder(): string
    {
        return <<<'PHP'
        // Replace with your real data source, e.g.:
        //
        // return CalendarEvent::fromCollection(
        //     Meeting::whereBetween('starts_at', [$from, $to])->get(),
        //     startKey: 'starts_at',
        //     endKey:   'ends_at',
        // );

        return [
            new CalendarEvent(
                id:    1,
                title: 'Sample event',
                start: now()->setHour(10)->setMinute(0),
                end:   now()->setHour(11)->setMinute(0),
                color: 'blue',
                description: 'Replace this with real events from your database.',
            ),
        ];
PHP;
    }

    protected function alias(string $class): string
    {
        return 'jaren.' . Str::kebab($class);
    }
}
