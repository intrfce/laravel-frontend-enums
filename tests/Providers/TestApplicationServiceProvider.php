<?php

namespace Intrfce\LaravelFrontendEnums\Tests\Providers;

use Illuminate\Support\ServiceProvider;
use Intrfce\LaravelFrontendEnums\Facades\PublishEnums;

class TestApplicationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        PublishEnums::discoverIn(__DIR__ . '/../Enums')
            ->toDirectory(resource_path('js/Enums'));
    }

    public function boot(): void {}
}
