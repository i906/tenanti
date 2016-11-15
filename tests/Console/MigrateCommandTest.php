<?php namespace Orchestra\Tenanti\TestCase\Console;

use Mockery as m;
use Symfony\Component\Console\Exception\RuntimeException;

class MigrateCommandTest extends CommandTest
{
    public function testMigrateWithoutAnyDrivers()
    {
        $tenanti = $this->app['orchestra.tenanti'];

        $tenanti->shouldReceive('getConfig')
            ->andReturn([]);

        $command = m::mock('\Orchestra\Tenanti\Console\MigrateCommand[prepareDatabase]', [$tenanti]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('missing: "driver"');

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:migrate');
    }

    public function testMigrateWithExistingDriver()
    {
        $tenanti = $this->app['orchestra.tenanti'];

        $tenanti->shouldReceive('driver')
            ->with('driver')
            ->andReturn($this->getMockDriverFactory());

        $command = m::mock('\Orchestra\Tenanti\Console\MigrateCommand[prepareDatabase]', [$tenanti]);

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:migrate', ['driver' => 'driver']);
    }

    public function testMigrateWithNonExistingDriver()
    {
        $tenanti = $this->app['orchestra.tenanti'];

        $tenanti->shouldReceive('driver')
            ->with('ghost')
            ->andThrow(RuntimeException::class, 'Driver [ghost] not supported.');

        $command = m::mock('\Orchestra\Tenanti\Console\MigrateCommand[prepareDatabase]', [$tenanti]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('[ghost] not supported');

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:migrate', ['driver' => 'ghost']);
    }

    public function testMigrateWithoutDriverArgumentAndGetFromConfig()
    {
        $tenanti = $this->app['orchestra.tenanti'];

        $tenanti->shouldReceive('getConfig')
            ->andReturn([
                'tenant' => [
                ],
            ]);

        $tenanti->shouldReceive('driver')
            ->with('tenant')
            ->andReturn($this->getMockDriverFactory());

        $command = m::mock('\Orchestra\Tenanti\Console\MigrateCommand[prepareDatabase]', [$tenanti]);

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:migrate');
    }

    public function testMigrateWithoutDriverArgumentAndRejectFromConfig()
    {
        $tenanti = $this->app['orchestra.tenanti'];

        $tenanti->shouldReceive('getConfig')
            ->andReturn([
                'tenant1' => [
                ],
                'tenant2' => [
                ],
            ]);

        $command = m::mock('\Orchestra\Tenanti\Console\MigrateCommand[prepareDatabase]', [$tenanti]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('missing: "driver"');

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:migrate');
    }
}
