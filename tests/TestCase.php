<?php

namespace Intrfce\LaravelFrontendEnums\Tests;

use Intrfce\LaravelFrontendEnums\LaravelFrontendEnumsServiceProvider;
use Intrfce\LaravelFrontendEnums\Tests\Providers\TestApplicationServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase {

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