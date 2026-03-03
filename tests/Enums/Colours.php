<?php

namespace Intrfce\LaravelFrontendEnums\Tests\Enums;

use Intrfce\LaravelFrontendEnums\Attributes\PublishEnum;

#[PublishEnum]
enum Colours: string
{
    case Red = 'red';
    case Blue = 'blue';
}
