<?php

namespace Intrfce\LaravelFrontendEnums;

use Intrfce\LaravelFrontendEnums\Attributes\PublishEnum;
use ReflectionClass;
use Spatie\StructureDiscoverer\Discover;

class Registry
{
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

    public function discoverIn(string ...$directories): self
    {
        $this->discoveryDirectories = array_merge($this->discoveryDirectories, $directories);

        return $this;
    }

    public function get(): array
    {
        return array_values(array_unique($this->discoverEnumsWithAttribute()));
    }

    public function isTypescript(string $enumClass): bool
    {
        $reflection = new ReflectionClass($enumClass);
        $attributes = $reflection->getAttributes(PublishEnum::class);

        if (! empty($attributes)) {
            $attrValue = $attributes[0]->newInstance()->asTypescript;
            if ($attrValue !== null) {
                return $attrValue;
            }
        }

        return config('laravel-frontend-enums.as_typescript', false);
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

}
