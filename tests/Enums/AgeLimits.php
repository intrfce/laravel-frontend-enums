<?php

namespace Intrfce\LaravelFrontendEnums\Tests\Enums;

use Intrfce\LaravelFrontendEnums\Attributes\PublishEnum;

#[PublishEnum]
enum AgeLimits: int
{
    case Smoking = 18;
    case Driving = 17;
}
