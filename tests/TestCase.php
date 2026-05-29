<?php

namespace FluxUI\Tests;

use FluxUI\FluxUIServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            FluxUIServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'FluxUI' => \FluxUI\Facades\FluxUI::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('view.paths', [
            __DIR__ . '/../resources/views',
            resource_path('views'),
        ]);
    }
}
