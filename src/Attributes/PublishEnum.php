<?php

namespace Intrfce\LaravelFrontendEnums\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class PublishEnum
{
    public function __construct(
        public ?bool $asTypescript = null,
    ) {}
}
