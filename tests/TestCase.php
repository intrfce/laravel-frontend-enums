<?php

namespace Intrfce\LaravelFrontendEnums\Tests;

use Intrfce\LaravelFrontendEnums\LaravelFrontendEnumsServiceProvider;
use Intrfce\LaravelFrontendEnums\Tests\Providers\TestApplicationServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelFrontendEnumsServiceProvider::class,
            TestApplicationServiceProvider::class,
        ];
    }
}
