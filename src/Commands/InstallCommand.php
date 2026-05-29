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

        // 1. Publish config
        if (! $this->option('no-config')) {
            $this->publishConfig();
        }

        // 2. Optionally publish views
        if ($this->option('views')) {
            $this->publishViews();
        }

        // 3. Add @jarenStyles to layout if found
        $this->injectJarenStyles();

        // 4. Add <livewire:jaren.toast/> to layout if found
        $this->injectToastComponent();

        // 5. Add @source and @import jarenui.css to app.css
        $this->injectTailwindSource();

        $this->newLine();
        $this->components->success('JarenUI installed successfully!');

        $this->line('');
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
        $source   = '@source "../../vendor/leenuxus/jarenui/resources/views";';
        $import   = '@import "../../vendor/leenuxus/jarenui/resources/css/jarenui.css";';

        // ── @source ──────────────────────────────────────────────────────────────
        if (str_contains($contents, 'leenuxus/jarenui/resources/views')) {
            $this->components->twoColumnDetail('Skipped Tailwind @source injection', '<fg=yellow>already present</>');
        } else {
            if (str_contains($contents, '@import "tailwindcss"')) {
                $contents = str_replace(
                    '@import "tailwindcss";',
                    '@import "tailwindcss";' . PHP_EOL . $source,
                    $contents
                );
            } elseif (str_contains($contents, "@import 'tailwindcss'")) {
                $contents = str_replace(
                    "@import 'tailwindcss';",
                    "@import 'tailwindcss';" . PHP_EOL . $source,
                    $contents
                );
            } else {
                $contents = $source . PHP_EOL . PHP_EOL . $contents;
            }

            $this->components->task('Injecting Tailwind @source into app.css');
        }

        // ── @source ──────────────────────────────────────────────────────────────
        if (str_contains($contents, 'leenuxus/jarenui/resources/views')) {
            $this->components->twoColumnDetail('Skipped Tailwind @source injection', '<fg=yellow>already present</>');
        } else {
            if (str_contains($contents, '@import "tailwindcss"')) {
                $contents = str_replace(
                    '@import "tailwindcss";',
                    '@import "tailwindcss";' . PHP_EOL . $source,
                    $contents
                );
            } elseif (str_contains($contents, "@import 'tailwindcss'")) {
                $contents = str_replace(
                    "@import 'tailwindcss';",
                    "@import 'tailwindcss';" . PHP_EOL . $source,
                    $contents
                );
            } else {
                $contents = $source . PHP_EOL . PHP_EOL . $contents;
            }

            $this->components->task('Injecting Tailwind @source into app.css');
        }

        // ── @import jarenui.css ───────────────────────────────────────────────────
        if (str_contains($contents, 'jarenui/resources/css/jarenui.css')) {
            $this->components->twoColumnDetail('Skipped jarenui.css import', '<fg=yellow>already present</>');
        } else {
            // Add after @source line we just added (or at end of imports block)
            $contents = str_replace(
                $source,
                $source . PHP_EOL . $import,
                $contents
            );

            $this->components->task('Injecting jarenui.css import into app.css');
        }

        File::put($css, $contents);
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
