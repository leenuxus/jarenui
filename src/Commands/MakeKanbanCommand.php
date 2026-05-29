<?php

namespace JarenUI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeKanbanCommand extends Command
{
    protected $signature = 'jaren:make-kanban
                            {name    : Class name, e.g. ProjectKanban}
                            {--force : Overwrite existing file}';

    protected $description = 'Generate a new JarenUI Kanban Livewire component';

    public function handle(): int
    {
        $name  = $this->argument('name');
        $class = Str::studly($name);
        $path  = app_path("Livewire/{$class}.php");

        if (File::exists($path) && ! $this->option('force')) {
            $this->components->error("File [{$path}] already exists. Use --force to overwrite.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->buildClass($class));

        $this->components->info("JarenUI Kanban created: [app/Livewire/{$class}.php]");
        $this->line('');
        $this->line("  Use in Blade:  <fg=cyan><livewire:{$this->componentAlias($class)}/></>");
        $this->line("  Customise:     Edit the mount() method to load your data.");
        $this->line('');

        return self::SUCCESS;
    }

    protected function buildClass(string $class): string
    {
        return <<<PHP
<?php

namespace App\Livewire;

use JarenUI\Livewire\Kanban;

class {$class} extends Kanban
{
    public function mount(): void
    {
        \$this->columns = [
            ['id' => 'todo',        'label' => 'To Do',       'color' => '#9a9894'],
            ['id' => 'in_progress', 'label' => 'In Progress', 'color' => '#d97706', 'limit' => 3],
            ['id' => 'review',      'label' => 'Review',      'color' => '#2563eb'],
            ['id' => 'done',        'label' => 'Done',        'color' => '#16a34a'],
        ];

        // Load cards from your database:
        // \$this->cards = Task::all()->map(fn (\$t) => [
        //     'id'             => \$t->id,
        //     'column'         => \$t->status,
        //     'title'          => \$t->title,
        //     'tag'            => \$t->type,
        //     'tag_color'      => 'blue',
        //     'assignee'       => \$t->assignee?->initials,
        //     'assignee_color' => '#2563eb',
        //     'due'            => \$t->due_date,
        //     'comments'       => \$t->comments_count,
        // ])->toArray();

        \$this->cards = [
            ['id' => 1, 'column' => 'todo',        'title' => 'Example card', 'tag' => 'Feature', 'tag_color' => 'blue'],
            ['id' => 2, 'column' => 'in_progress',  'title' => 'Work in progress', 'tag' => 'Bug',     'tag_color' => 'red'],
        ];
    }

    /**
     * Persist card move to the database.
     * Called automatically when a card is dragged to a new column.
     */
    public function moveCard(int \$cardId, string \$targetColumn, ?int \$afterCardId = null): void
    {
        parent::moveCard(\$cardId, \$targetColumn, \$afterCardId);

        // Persist to DB:
        // Task::find(\$cardId)?->update(['status' => \$targetColumn]);
    }
}
PHP;
    }

    protected function componentAlias(string $class): string
    {
        return Str::of($class)
            ->kebab()
            ->prepend('jaren.')
            ->toString();
    }
}
