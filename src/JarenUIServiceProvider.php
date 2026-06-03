<?php

namespace JarenUI;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use JarenUI\Commands\InstallCommand;
use JarenUI\Commands\MakeEventCalendarCommand;
use JarenUI\Commands\MakeComboboxCommand;
use JarenUI\Commands\MakeWizardCommand;
use JarenUI\Commands\PublishCommand;
use JarenUI\Commands\MakeTableCommand;
use JarenUI\Commands\MakeKanbanCommand;
use JarenUI\Livewire\Casts\DateRangeSynth;

class JarenUIServiceProvider extends ServiceProvider
{
    /**
     * Anonymous Blade components registered under the <x-jaren::*> namespace.
     * Maps component alias → view path (relative to resources/views/components/jaren/).
     */
    protected array $bladeComponents = [
        // Primitives
        'button'       => 'button',
        'badge'        => 'badge',
        'avatar'       => 'avatar',
        'brand'        => 'brand',
        'heading'      => 'heading',
        'text'         => 'text',
        'separator'    => 'separator',

        // Form
        'input'        => 'input',
        'textarea'     => 'textarea',
        'select'       => 'select',
        'checkbox'     => 'checkbox',
        'radio'        => 'radio',
        'radio-group'  => 'radio-group',
        'switch'       => 'switch',
        'slider'       => 'slider',
        'otp-input'    => 'otp-input',
        'pillbox'      => 'pillbox',
        'field'        => 'field',

        // Navigation
        'accordion'           => 'accordion',
        'accordion.item'      => 'accordion.item',
        'breadcrumbs'         => 'breadcrumbs',
        'dropdown'            => 'dropdown',
        'dropdown.item'       => 'dropdown.item',
        'dropdown.separator'  => 'dropdown.separator',
        'dropdown.group'      => 'dropdown.group',
        'navbar'              => 'navbar',
        'navbar.item'         => 'navbar.item',
        'pagination'          => 'pagination',
        'tabs'                => 'tabs',
        'tabs.tab'            => 'tabs.tab',
        'tabs.panel'          => 'tabs.panel',

        // Overlay
        'modal'    => 'modal',
        'popover'  => 'popover',
        'tooltip'  => 'tooltip',

        // Feedback
        'callout'  => 'callout',
        'progress' => 'progress',
        'skeleton' => 'skeleton',

        // Display
        'card'        => 'card',
        'profile'     => 'profile',
        'timeline'    => 'timeline',
        'timeline.item' => 'timeline.item',

        // Layout
        'header'         => 'header',
        'navbar'         => 'navbar',
        'sidebar'        => 'sidebar',
        'sidebar.section'=> 'sidebar.section',
        'sidebar.item'   => 'sidebar.item',
        'sidebar.user'   => 'sidebar.user',

        // Advanced UI components.
        'calendar'      => 'calendar',
        'wizard'        => 'wizard',
        'combobox'      => 'combobox',
    ];

    /**
     * Livewire full-stack components (PHP class + Blade view).
     */
    protected array $livewireComponents = [
        'jaren.toast'  => \JarenUI\Livewire\Toast::class,
        'jaren.table'  => \JarenUI\Livewire\Table::class,
        'jaren.kanban' => \JarenUI\Livewire\Kanban::class,
        'jaren.event-calendar' => \JarenUI\Livewire\EventCalendar::class,
        'jaren.wizard'         => \JarenUI\Livewire\Wizard::class,
        'jaren.async-combobox' => \JarenUI\Livewire\AsyncCombobox::class,
    ];

    // ──────────────────────────────────────────────────────────────────────────

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/jarenui.php',
            'jarenui'
        );

        // Bind the JarenUI singleton (used by Facade)
        $this->app->singleton('jarenui', fn () => new JarenUI());
    }

    public function boot(): void
    {
        // ── Views ──────────────────────────────────────────────────────────────
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'jarenui'
        );

        // ── Blade component namespace ──────────────────────────────────────────
        // Allows <x-jarenui::button> shorthand alongside <x-jaren::button>
        Blade::componentNamespace('JarenUI\\View\\Components', 'jarenui');

        // Register each anonymous component under the "jaren" prefix
        // so <x-jaren::button> resolves to jarenui::components.jaren.button
        Blade::anonymousComponentPath(
            __DIR__ . '/../resources/views/components/jaren',
            'jaren'
        );

        // ── Livewire components ────────────────────────────────────────────────
        foreach ($this->livewireComponents as $name => $class) {
            Livewire::component($name, $class);
        }

        // Register DateRange synth so wire:model works with ?DateRange properties
        Livewire::propertySynthesizer(DateRangeSynth::class);

        // ── Blade directives ───────────────────────────────────────────────────
        $this->registerBladeDirectives();

        // ── Publishable assets ─────────────────────────────────────────────────
        $this->registerPublishables();

        // ── Artisan commands ───────────────────────────────────────────────────
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                PublishCommand::class,
                MakeTableCommand::class,
                MakeKanbanCommand::class,
                MakeEventCalendarCommand::class,
                MakeWizardCommand::class,
                MakeComboboxCommand::class,
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────────────

    protected function registerBladeDirectives(): void
    {
        /**
         * @jarenStyles
         * Injects the JarenUI CSS token stylesheet.
         * Add inside <head> of your layout.
         *
         * Usage:  @jarenStyles
         */
        Blade::directive('jarenStyles', function () {
            $config = config('jarenui', []);
            $theme  = $config['default_theme'] ?? 'light';
            $accent = $config['accent_color']  ?? null;

            $inline = $accent
                ? "<style>:root{--accent:{$accent};}</style>"
                : '';

            $link = sprintf(
                '<link rel="stylesheet" href="%s">',
                asset('vendor/jarenui/css/jarenui.css')
            );

            return "<?php echo '{$inline}{$link}'; ?>";
        });

        /**
         * @jarenScripts
         * Optional: Injects any JarenUI JS (currently empty, uses Alpine).
         */
        Blade::directive('jarenScripts', function () {
            return ''; // Alpine.js is loaded by the host app
        });
    }

    protected function registerPublishables(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // CSS assets → public/vendor/jarenui
        $this->publishes([
            __DIR__ . '/../resources/css' => public_path('vendor/jarenui/css'),
        ], ['jarenui', 'jarenui-assets']);

        // Config
        $this->publishes([
            __DIR__ . '/../config/jarenui.php' => config_path('jarenui.php'),
        ], ['jarenui', 'jarenui-config']);

        // Blade views (for customisation)
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/jarenui'),
        ], ['jarenui', 'jarenui-views']);

        // Layout stubs
        $this->publishes([
            __DIR__ . '/../stubs/layouts' => resource_path('views/layouts'),
        ], ['jarenui', 'jarenui-stubs']);

        // Everything at once
        $this->publishes([
            __DIR__ . '/../resources/css'  => public_path('vendor/jarenui/css'),
            __DIR__ . '/../config/jarenui.php' => config_path('jarenui.php'),
        ], 'jarenui-install');
    }
}
