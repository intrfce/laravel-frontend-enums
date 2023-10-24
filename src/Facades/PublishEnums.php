<?php

namespace Intrfce\LaravelFrontendEnums\Facades;

use Illuminate\Support\Facades\Facade;

class PublishEnums extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'publish_enums_registry';
    }
}
