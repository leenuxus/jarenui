<?php

namespace JarenUI\Tests;

use JarenUI\JarenUIServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            JarenUIServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'JarenUI' => \JarenUI\Facades\JarenUI::class,
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
