<?php

namespace Intrfce\LaravelFrontendEnums\Tests\Enums;

use Intrfce\LaravelFrontendEnums\Attributes\PublishEnum;

#[PublishEnum(asTypescript: true)]
enum Statuses: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
