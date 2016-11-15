<?php

namespace Orchestra\Tenanti\TestCase\Console;

use Illuminate\Support\Composer;
use Mockery as m;
use Symfony\Component\Console\Exception\RuntimeException;

class MigrateMakeCommandTest extends CommandTest
{
    public function testMakeWithoutAnyDrivers()
    {
        $tenanti = $this->app['orchestra.tenanti'];
        $creator = $this->app['orchestra.tenanti.creator'];
        $composer = m::mock(Composer::class);

        $tenanti->shouldReceive('getConfig')
            ->andReturn([]);

        $command = m::mock('\Orchestra\Tenanti\Console\MigrateMakeCommand[call]', [$tenanti, $creator, $composer]);
        $command->shouldReceive('call');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('missing: "driver, name"');

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:make');
    }

    public function testMakeWithOneDriverWithOneArgument()
    {
        $tenanti = $this->app['orchestra.tenanti'];
        $creator = $this->app['orchestra.tenanti.creator'];

        $composer = m::mock(Composer::class);
        $composer->shouldReceive('dumpAutoloads');

        $tenanti->shouldReceive('getConfig')
            ->andReturn([
                'tenant' => [
                ],
            ]);

        $factory = $this->getMockDriverFactory();

        $tenanti->shouldReceive('driver')
            ->with('tenant')
            ->andReturn($factory);

        $command = m::mock('Orchestra\Tenanti\Console\MigrateMakeCommand[writeMigration]',
            [$tenanti, $creator, $composer])
            ->shouldAllowMockingProtectedMethods();

        $command->shouldReceive('writeMigration')
            ->withArgs(['tenant', 'add_migration', null, null])
            ->once();

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:make', ['driver' => 'add_migration']);
    }

    public function testTinkerWithOneDriverWithTwoArguments()
    {
        $tenanti = $this->app['orchestra.tenanti'];
        $creator = $this->app['orchestra.tenanti.creator'];

        $composer = m::mock(Composer::class);
        $composer->shouldReceive('dumpAutoloads');

        $tenanti->shouldReceive('getConfig')
            ->andReturn([
                'tenant1' => [
                ],
            ]);

        $command = m::mock('Orchestra\Tenanti\Console\MigrateMakeCommand[writeMigration]',
            [$tenanti, $creator, $composer])
            ->shouldAllowMockingProtectedMethods();

        $command->shouldReceive('writeMigration')
            ->withArgs(['tenant1', 'add_migration', null, null])
            ->once();

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:make', ['driver' => 'tenant1', 'name' => 'add_migration']);
    }

    public function testTinkerWithTwoDriversWithOneArgument()
    {
        $tenanti = $this->app['orchestra.tenanti'];
        $creator = $this->app['orchestra.tenanti.creator'];

        $composer = m::mock(Composer::class);
        $composer->shouldReceive('dumpAutoloads');

        $tenanti->shouldReceive('getConfig')
            ->andReturn([
                'tenant1' => [
                ],
                'tenant2' => [
                ],
            ]);

        $command = m::mock('Orchestra\Tenanti\Console\MigrateMakeCommand[writeMigration]',
            [$tenanti, $creator, $composer])
            ->shouldAllowMockingProtectedMethods();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('missing: "driver"');

        $command->shouldNotReceive('writeMigration');

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:make', ['driver' => 'add_migration']);
    }

    public function testTinkerWithTwoDriversWithTwoArguments()
    {
        $tenanti = $this->app['orchestra.tenanti'];
        $creator = $this->app['orchestra.tenanti.creator'];

        $composer = m::mock(Composer::class);
        $composer->shouldReceive('dumpAutoloads');

        $tenanti->shouldReceive('getConfig')
            ->andReturn([
                'tenant1' => [
                ],
                'tenant2' => [
                ],
            ]);

        $command = m::mock('Orchestra\Tenanti\Console\MigrateMakeCommand[writeMigration]',
            [$tenanti, $creator, $composer])
            ->shouldAllowMockingProtectedMethods();

        $command->shouldReceive('writeMigration')
            ->withArgs(['tenant2', 'add_migration', null, null])
            ->once();

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:make', ['driver' => 'tenant2', 'name' => 'add_migration']);
    }
}
