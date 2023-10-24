<?php

namespace Intrfce\LaravelFrontendEnums;

use Intrfce\LaravelFrontendEnums\Exceptions\ArgumentIsNotEnumException;
use ReflectionClass;

class Registry
{

    public array $toPublish = [];

    public bool $asTypescript = false;

    public string $publishPath = '';

    public function __construct()
    {
        $this->publishPath = resource_path('js/Enums');
    }

    public function setPublishPath(string $path): self
    {
        $this->publishPath = $path;
        return $this;
    }

    public function publish(array $items): self
    {

        $this->validateAllEnums($items);

        $this->toPublish = array_merge($this->toPublish, $items);
        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws ArgumentIsNotEnumException
     */
    protected function validateAllEnums(array $items): void
    {
        foreach ($items as $enumClass) {
            if (!is_string($enumClass) || !enum_exists($enumClass)) {
                throw new ArgumentIsNotEnumException("Class {$enumClass} is not an enum.");
            }
        }
    }

    public function get(): array
    {
        return $this->toPublish;
    }

    public function asTypescript(): bool
    {
        return $this->asTypescript;
    }

    public function toDirectory(string $path): self
    {
        $this->publishPath = $path;
        return $this;
    }
}
