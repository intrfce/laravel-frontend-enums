<?php

namespace Intrfce\LaravelFrontendEnums\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Intrfce\LaravelFrontendEnums\LaravelFrontendEnumsServiceProvider;
use Intrfce\LaravelFrontendEnums\Tests\Providers\TestApplicationServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelFrontendEnumsServiceProvider::class,
            TestApplicationServiceProvider::class,
        ];
    }
}
