<?php

namespace JarenUI\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    protected $signature = 'jaren:publish
                            {--views   : Publish Blade views for customisation}
                            {--config  : Publish the config file}
                            {--assets  : Publish CSS/JS public assets}
                            {--stubs   : Publish layout stubs}
                            {--all     : Publish everything}
                            {--force   : Overwrite existing published files}';

    protected $description = 'Publish JarenUI assets, views, config, or layout stubs';

    public function handle(): int
    {
        $published = false;

        if ($this->option('all') || $this->option('assets')) {
            $this->call('vendor:publish', [
                '--tag'   => 'jarenui-assets',
                '--force' => $this->option('force'),
            ]);
            $published = true;
        }

        if ($this->option('all') || $this->option('config')) {
            $this->call('vendor:publish', [
                '--tag'   => 'jarenui-config',
                '--force' => $this->option('force'),
            ]);
            $published = true;
        }

        if ($this->option('all') || $this->option('views')) {
            $this->call('vendor:publish', [
                '--tag'   => 'jarenui-views',
                '--force' => $this->option('force'),
            ]);

            $this->newLine();
            $this->components->info('Views published to resources/views/vendor/jarenui/');
            $this->line('  Laravel will now use your local copies. Edit freely!');
            $this->line('  To revert to package defaults, delete the files and run jaren:publish --views again.');
            $published = true;
        }

        if ($this->option('all') || $this->option('stubs')) {
            $this->call('vendor:publish', [
                '--tag'   => 'jarenui-stubs',
                '--force' => $this->option('force'),
            ]);
            $published = true;
        }

        if (! $published) {
            $choice = $this->choice(
                'What would you like to publish?',
                [
                    'assets'  => 'CSS assets  → public/vendor/jarenui/',
                    'config'  => 'Config      → config/jarenui.php',
                    'views'   => 'Blade views → resources/views/vendor/jarenui/',
                    'stubs'   => 'Layout stubs → resources/views/layouts/',
                    'all'     => 'Everything',
                ],
                'assets'
            );

            $this->call('vendor:publish', [
                '--tag'   => $choice === 'all' ? 'jarenui' : "jarenui-{$choice}",
                '--force' => $this->option('force'),
            ]);
        }

        return self::SUCCESS;
    }
}
