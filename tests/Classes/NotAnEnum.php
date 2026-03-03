<?php

namespace Intrfce\LaravelFrontendEnums\Tests\Classes;

use Intrfce\LaravelFrontendEnums\Attributes\PublishEnum;

#[PublishEnum]
class NotAnEnum
{
    public const ACTIVE = 'active';
}
