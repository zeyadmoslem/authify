<?php

namespace Deudev\Authify\Tests;

use Deudev\Authify\AuthifyServiceProvider;
use Mockery;
use Orchestra\Testbench\TestCase;

abstract class OrchestraTestCase extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    protected function getPackageProviders($app)
    {
        return [AuthifyServiceProvider::class];
    }
}
