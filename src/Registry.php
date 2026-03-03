<?php

namespace Intrfce\LaravelFrontendEnums;

use Intrfce\LaravelFrontendEnums\Attributes\PublishEnum;
use Intrfce\LaravelFrontendEnums\Exceptions\ArgumentIsNotEnumException;
use ReflectionClass;
use ReflectionException;
use Spatie\StructureDiscoverer\Discover;

class Registry
{
    public array $toPublish = [];

    public bool $asTypescript = false;

    public string $publishPath = '';

    public array $discoveryDirectories = [];

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

    public function discoverIn(string ...$directories): self
    {
        $this->discoveryDirectories = array_merge($this->discoveryDirectories, $directories);

        return $this;
    }

    public function get(): array
    {
        return array_values(array_unique(
            array_merge($this->toPublish, $this->discoverEnumsWithAttribute())
        ));
    }

    public function asTypescript(): self
    {
        $this->asTypescript = true;

        return $this;
    }

    public function isTypescript(string $enumClass): bool
    {
        if ($this->asTypescript) {
            return true;
        }

        $reflection = new ReflectionClass($enumClass);
        $attributes = $reflection->getAttributes(PublishEnum::class);

        if (empty($attributes)) {
            return false;
        }

        return $attributes[0]->newInstance()->asTypescript;
    }

    public function toDirectory(string $path): self
    {
        $this->publishPath = $path;

        return $this;
    }

    protected function discoverEnumsWithAttribute(): array
    {
        $configDirectories = config('laravel-frontend-enums.discover_in', [app_path()]);
        $directories = array_merge($this->discoveryDirectories, $configDirectories);

        $resolved = [];
        foreach ($directories as $dir) {
            if (str_contains($dir, '*') || str_contains($dir, '?') || str_contains($dir, '[')) {
                $resolved = array_merge($resolved, glob($dir, GLOB_ONLYDIR) ?: []);
            } else {
                $resolved[] = $dir;
            }
        }

        $existing = array_values(array_unique(array_filter($resolved, fn (string $dir) => is_dir($dir))));

        if (empty($existing)) {
            return [];
        }

        return Discover::in(...$existing)
            ->enums()
            ->withAttribute(PublishEnum::class)
            ->get();
    }

    /**
     * @throws ReflectionException
     * @throws ArgumentIsNotEnumException
     */
    protected function validateAllEnums(array $items): void
    {
        foreach ($items as $enumClass) {
            if (! is_string($enumClass) || ! enum_exists($enumClass)) {
                throw new ArgumentIsNotEnumException("Class {$enumClass} is not an enum.");
            }
        }
    }
}
