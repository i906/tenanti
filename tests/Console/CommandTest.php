<?php namespace Orchestra\Tenanti\TestCase\Console;

use Illuminate\Support\Composer;
use Mockery as m;
use Orchestra\Tenanti\CommandServiceProvider;
use Orchestra\Tenanti\Migrator\Creator;
use Orchestra\Tenanti\Migrator\FactoryInterface;
use Orchestra\Tenanti\TenantiManager;
use Orchestra\Tenanti\TestCase\Kernel;
use Orchestra\Testbench\TestCase;

abstract class CommandTest extends TestCase
{
    protected function getMockDriverFactory() {
        $factory = m::mock(FactoryInterface::class);

        $factory->shouldReceive('install');

        $factory->shouldReceive('getNotes')
            ->andReturn([]);

        $factory->shouldReceive('flushNotes');

        $factory->shouldReceive('run');

        return $factory;
    }

    protected function getPackageProviders($app)
    {
        return [
            CommandServiceProvider::class,
        ];
    }

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('artisan', function ($app) {
            return new \Illuminate\Console\Application($app, $app['events'], $app->version());
        });

        $app->singleton('Illuminate\Contracts\Console\Kernel', Kernel::class);

        $app['orchestra.tenanti.creator'] = m::mock(Creator::class);
        $app['orchestra.tenanti'] = m::mock(TenantiManager::class);
    }

    public function artisan($command, $parameters = [])
    {
        parent::artisan($command, array_merge($parameters, ['--no-interaction' => true]));
    }
}
