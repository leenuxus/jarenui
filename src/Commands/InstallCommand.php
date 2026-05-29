<?php

namespace JarenUI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'jaren:install
                            {--force     : Overwrite existing files}
                            {--no-css    : Skip publishing CSS assets}
                            {--no-config : Skip publishing config}
                            {--views     : Also publish Blade views for customisation}';

    protected $description = 'Install JarenUI — publish CSS assets and config';

    public function handle(): int
    {
        $this->components->info('Installing JarenUI Livewire Component Library…');

        // 1. Publish CSS assets
        if (! $this->option('no-css')) {
            $this->publishAssets();
        }

        // 2. Publish config
        if (! $this->option('no-config')) {
            $this->publishConfig();
        }

        // 3. Optionally publish views
        if ($this->option('views')) {
            $this->publishViews();
        }

        // 4. Add @jarenStyles to layout if found
        $this->injectJarenStyles();

        // 5. Add <livewire:jaren.toast/> to layout if found
        $this->injectToastComponent();

        // 6. Add @source directive to app.css for Tailwind v4
        $this->injectTailwindSource();

        $this->newLine();
        $this->components->success('JarenUI installed successfully!');

        $this->line('');
        $this->line('  <fg=green>✓</> CSS tokens: <fg=cyan>public/vendor/jarenui/css/jarenui.css</>');
        $this->line('  <fg=green>✓</> Config:     <fg=cyan>config/jarenui.php</>');
        $this->line('');
        $this->line('  <fg=yellow>Next steps:</>');
        $this->line('  1. Add <fg=cyan>@jarenStyles</> inside <head> of your layout (if not auto-injected)');
        $this->line('  2. Add <fg=cyan><livewire:jaren.toast/></> before </body> (if not auto-injected)');
        $this->line('  3. Set <fg=cyan>JARENUI_ACCENT</> in .env to customise your brand colour');
        $this->line('  4. Run <fg=cyan>php artisan jaren:publish --help</> to explore more options');
        $this->line('');

        return self::SUCCESS;
    }

    protected function publishAssets(): void
    {
        $this->components->task('Publishing CSS assets', function () {
            $this->callSilently('vendor:publish', [
                '--tag'   => 'jarenui-assets',
                '--force' => $this->option('force'),
            ]);
        });
    }

    protected function publishConfig(): void
    {
        $target = config_path('jarenui.php');

        if (File::exists($target) && ! $this->option('force')) {
            if (! $this->components->confirm('config/jarenui.php already exists. Overwrite?', false)) {
                return;
            }
        }

        $this->components->task('Publishing config', function () {
            $this->callSilently('vendor:publish', [
                '--tag'   => 'jarenui-config',
                '--force' => true,
            ]);
        });
    }

    protected function publishViews(): void
    {
        $this->components->task('Publishing Blade views', function () {
            $this->callSilently('vendor:publish', [
                '--tag'   => 'jarenui-views',
                '--force' => $this->option('force'),
            ]);
        });
    }

    protected function injectJarenStyles(): void
    {
        $layout = $this->findLayout();
        if (! $layout) {
            return;
        }

        $contents = File::get($layout);

        if (str_contains($contents, '@jarenStyles')) {
            $this->components->twoColumnDetail('Skipped @jarenStyles injection', '<fg=yellow>already present</>');
            return;
        }

        $updated = str_replace(
            '<head>',
            "<head>\n    @jarenStyles",
            $contents
        );

        if ($updated !== $contents) {
            File::put($layout, $updated);
            $this->components->task('Injecting @jarenStyles into layout');
        }
    }

    protected function injectToastComponent(): void
    {
        $layout = $this->findLayout();
        if (! $layout) {
            return;
        }

        $contents = File::get($layout);

        if (str_contains($contents, 'jaren.toast') || str_contains($contents, 'jaren::toast')) {
            $this->components->twoColumnDetail('Skipped Toast injection', '<fg=yellow>already present</>');
            return;
        }

        $updated = str_replace(
            '</body>',
            "    <livewire:jaren.toast />\n</body>",
            $contents
        );

        if ($updated !== $contents) {
            File::put($layout, $updated);
            $this->components->task('Injecting <livewire:jaren.toast /> into layout');
        }
    }

    protected function injectTailwindSource(): void
    {
        $css = $this->findAppCss();
 
        if (! $css) {
            $this->components->twoColumnDetail(
                'Skipped Tailwind @source injection',
                '<fg=yellow>app.css not found — add manually:</> @source "../../vendor/leenuxus/jarenui/resources/views";'
            );
            return;
        }
 
        $contents = File::get($css);
        $directive = '@source "../../vendor/leenuxus/jarenui/resources/views";';
 
        if (str_contains($contents, 'leenuxus/jarenui/resources/views')) {
            $this->components->twoColumnDetail('Skipped Tailwind @source injection', '<fg=yellow>already present</>');
            return;
        }
 
        // Insert after @import "tailwindcss"; if present, otherwise prepend
        if (str_contains($contents, '@import "tailwindcss"')) {
            $updated = str_replace(
                '@import "tailwindcss";',
                '@import "tailwindcss";' . PHP_EOL . $directive,
                $contents
            );
        } elseif (str_contains($contents, "@import 'tailwindcss'")) {
            $updated = str_replace(
                "@import 'tailwindcss';",
                "@import 'tailwindcss';" . PHP_EOL . $directive,
                $contents
            );
        } else {
            // No tailwindcss import found — add at the top
            $updated = $directive . PHP_EOL . PHP_EOL . $contents;
        }
 
        File::put($css, $updated);
        $this->components->task('Injecting Tailwind @source into app.css');
    }

    protected function findAppCss(): ?string
    {
        $candidates = [
            resource_path('css/app.css'),
            resource_path('sass/app.scss'),
            base_path('resources/css/app.css'),
        ];
 
        foreach ($candidates as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }
 
        return null;
    }

    protected function findLayout(): ?string
    {
        $candidates = [
            resource_path('views/layouts/app.blade.php'),
            resource_path('views/components/layouts/app.blade.php'),
            resource_path('views/layout.blade.php'),
        ];

        foreach ($candidates as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }
}