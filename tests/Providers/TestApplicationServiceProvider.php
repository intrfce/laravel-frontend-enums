<?php

namespace Intrfce\LaravelFrontendEnums\Tests\Providers;

use Illuminate\Support\ServiceProvider;
use Intrfce\LaravelFrontendEnums\Facades\PublishEnums;
use Intrfce\LaravelFrontendEnums\Tests\Enums\AgeLimits;
use Intrfce\LaravelFrontendEnums\Tests\Enums\Colours;

class TestApplicationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        PublishEnums::publish([
            AgeLimits::class,
            Colours::class,
        ])
            ->toDirectory(resource_path('js/Enums'));
    }

    public function boot(): void {}
}
