<?php

namespace Intrfce\LaravelFrontendEnums\Tests\Enums;

use Intrfce\LaravelFrontendEnums\Attributes\PublishEnum;

#[PublishEnum]
enum Directions
{
    case North;
    case South;
    case East;
    case West;
}
