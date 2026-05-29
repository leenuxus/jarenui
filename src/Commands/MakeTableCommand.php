<?php

namespace JarenUI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeTableCommand extends Command
{
    protected $signature = 'jaren:make-table
                            {name       : Class name, e.g. UsersTable}
                            {--model=   : Eloquent model (e.g. User)}
                            {--force    : Overwrite existing file}';

    protected $description = 'Generate a new JarenUI Table Livewire component';

    public function handle(): int
    {
        $name  = $this->argument('name');
        $model = $this->option('model') ?? Str::of($name)->replace('Table', '')->singular()->toString();
        $class = Str::studly($name);
        $path  = app_path("Livewire/{$class}.php");

        if (File::exists($path) && ! $this->option('force')) {
            $this->components->error("File [{$path}] already exists. Use --force to overwrite.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->buildClass($class, $model));

        $this->components->info("JarenUI Table created: [app/Livewire/{$class}.php]");
        $this->line('');
        $this->line("  Use in Blade:  <fg=cyan><livewire:{$this->componentAlias($class)}/></>");
        $this->line("  Customise:     Edit \$columns, \$searchable, and applyFilters() in the class.");
        $this->line('');

        return self::SUCCESS;
    }

    protected function buildClass(string $class, string $model): string
    {
        $modelClass  = Str::studly($model);
        $modelImport = "App\\Models\\{$modelClass}";

        return <<<PHP
<?php

namespace App\Livewire;

use {$modelImport};
use JarenUI\Livewire\Table;

class {$class} extends Table
{
    /** The Eloquent model to paginate. */
    public string \$model = {$modelClass}::class;

    /** Column definitions. */
    public array \$columns = [
        ['key' => 'id',         'label' => 'ID',      'sortable' => true],
        ['key' => 'name',       'label' => 'Name',    'sortable' => true],
        ['key' => 'email',      'label' => 'Email',   'sortable' => true],
        ['key' => 'created_at', 'label' => 'Created', 'sortable' => true, 'format' => 'date'],
    ];

    /** Columns searched by the toolbar search box. */
    public array \$searchable = ['name', 'email'];

    /** Default sort column. */
    public string \$sortColumn = 'created_at';

    /**
     * Add custom query constraints, eager loads, or scopes here.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  \$query
     */
    protected function applyFilters(\$query): void
    {
        // Example: \$query->with('role')->where('active', true);
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
