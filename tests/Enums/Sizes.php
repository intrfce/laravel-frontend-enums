<?php

namespace Intrfce\LaravelFrontendEnums\Tests\Enums;

use Intrfce\LaravelFrontendEnums\Attributes\PublishEnum;

#[PublishEnum]
enum Sizes: string
{
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';
}
