<?php

use Illuminate\Support\Facades\File;
use Intrfce\LaravelFrontendEnums\Facades\PublishEnums;
use Intrfce\LaravelFrontendEnums\Tests\Classes\NotAnEnum;
use Intrfce\LaravelFrontendEnums\Tests\Enums\Directions;
use Intrfce\LaravelFrontendEnums\Tests\Enums\Sizes;
use Intrfce\LaravelFrontendEnums\Tests\Enums\Statuses;

afterEach(function () {
    collect(File::allFiles("tests/Publish"))
        ->reject(function ($file) {
            return str_contains(".gitkeep", $file->getFilename());
        })
        ->each(fn($file) => File::delete($file->getPathname()));

    File::deleteDirectory("tests/Publish/Nested");
});

test("The package registry discovers enums with #[PublishEnum] attribute", function () {
    $enumsToPublish = app("publish_enums_registry")->get();
    expect($enumsToPublish)->toHaveCount(5);
});

test(
    "It publishes the enums to the default directory at resources/js/Enums",
    function () {
        $path = getcwd() . "/tests/Publish";
        PublishEnums::setPublishPath($path);
        $this->artisan("publish:enums-to-javascript");

        collect(PublishEnums::get())->each(function ($enum) use ($path) {
            $name = new ReflectionClass($enum)->getShortName();
            $isTs = PublishEnums::isTypescript($enum);
            $ext = $isTs ? '.ts' : '.enum.js';
            $this->assertFileExists("{$path}/{$name}{$ext}");
            $contents = file_get_contents("{$path}/{$name}{$ext}");
            collect($enum::cases())->each(
                fn($case) => expect($contents)->toContain($case->name),
            );
        });
    },
);

test(
    "It publishes the enums to the default directory at resources/js/Enums as typescript files.",
    function () {
        $path = getcwd() . "/tests/Publish";
        config()->set('laravel-frontend-enums.as_typescript', true);
        PublishEnums::setPublishPath($path);
        $this->artisan("publish:enums-to-javascript");

        collect(PublishEnums::get())->each(function ($enum) use ($path) {
            $name = new ReflectionClass($enum)->getShortName();
            $this->assertFileExists("{$path}/{$name}.ts");
            $contents = file_get_contents("{$path}/{$name}.ts");
            collect($enum::cases())->each(
                fn($case) => expect($contents)
                    ->toContain($case->name)
                    ->and($contents)
                    ->toContain($case instanceof \BackedEnum ? (string) $case->value : $case->name)
                    ->and($contents)
                    ->toContain("export enum {$name} {"),
            );
        });
    },
);

test("that it creates any missing folders", function () {
    $path = getcwd() . "/tests/Publish/Nested";
    expect(File::isDirectory($path))->toBeFalse();
    PublishEnums::setPublishPath($path);
    $this->artisan("publish:enums-to-javascript");
    expect(File::isDirectory($path))->toBeTrue();
});

test(
    "enums with the #[PublishEnum] attribute are discovered and published",
    function () {
        $path = getcwd() . "/tests/Publish";

        // Point discovery at the test Enums directory.
        PublishEnums::discoverIn(__DIR__ . "/../Enums");
        PublishEnums::setPublishPath($path);

        $this->artisan("publish:enums-to-javascript");

        // The Sizes enum has #[PublishEnum] so it should be discovered and published.
        $name = new ReflectionClass(Sizes::class)->getShortName();
        $this->assertFileExists("{$path}/{$name}.enum.js");
        $contents = file_get_contents("{$path}/{$name}.enum.js");
        collect(Sizes::cases())->each(
            fn($case) => expect($contents)
                ->toContain($case->name)
                ->and($contents)
                ->toContain((string) $case->value)
                ->and($contents)
                ->toContain("export const {$name} = {")
                ->and($contents)
                ->not()
                ->toContain("} as const;"),
        );
    },
);

test(
    "PublishEnum attribute with asTypescript publishes as .ts while others remain .enum.js",
    function () {
        $path = getcwd() . "/tests/Publish";

        PublishEnums::discoverIn(__DIR__ . "/../Enums");
        PublishEnums::setPublishPath($path);

        $this->artisan("publish:enums-to-javascript");

        // Sizes has #[PublishEnum] (no asTypescript) -> should be .enum.js
        $sizesName = new ReflectionClass(Sizes::class)->getShortName();
        $this->assertFileExists("{$path}/{$sizesName}.enum.js");
        $sizesContents = file_get_contents("{$path}/{$sizesName}.enum.js");
        expect($sizesContents)->toContain("export const {$sizesName} = {");

        // Statuses has #[PublishEnum(asTypescript: true)] -> should be .ts
        $statusesName = new ReflectionClass(Statuses::class)->getShortName();
        $this->assertFileExists("{$path}/{$statusesName}.ts");
        $statusesContents = file_get_contents("{$path}/{$statusesName}.ts");
        expect($statusesContents)->toContain("export enum {$statusesName} {");
    },
);

test(
    "non-enum classes with #[PublishEnum] attribute are not discovered",
    function () {
        // Scan a directory that contains both enums and a regular class with #[PublishEnum].
        PublishEnums::discoverIn(
            __DIR__ . "/../Enums",
            __DIR__ . "/../Classes",
        );

        $all = PublishEnums::get();

        // NotAnEnum has #[PublishEnum] but is a class, not an enum — it should be excluded.
        expect($all)->not->toContain(NotAnEnum::class);
    },
);

test(
    "unit enums (non-backed) are published using case names as values",
    function () {
        $path = getcwd() . "/tests/Publish";

        PublishEnums::setPublishPath($path);

        $this->artisan("publish:enums-to-javascript");

        $name = new ReflectionClass(Directions::class)->getShortName();
        $this->assertFileExists("{$path}/{$name}.enum.js");
        $contents = file_get_contents("{$path}/{$name}.enum.js");

        expect($contents)
            ->toContain('North: "North"')
            ->and($contents)
            ->toContain('South: "South"')
            ->and($contents)
            ->toContain('East: "East"')
            ->and($contents)
            ->toContain('West: "West"')
            ->and($contents)
            ->not()
            ->toContain("} as const;");
    },
);

test(
    "unit enums (non-backed) are published correctly as typescript",
    function () {
        $path = getcwd() . "/tests/Publish";

        config()->set('laravel-frontend-enums.as_typescript', true);
        PublishEnums::setPublishPath($path);

        $this->artisan("publish:enums-to-javascript");

        $name = new ReflectionClass(Directions::class)->getShortName();
        $this->assertFileExists("{$path}/{$name}.ts");
        $contents = file_get_contents("{$path}/{$name}.ts");

        expect($contents)
            ->toContain('North = "North"')
            ->and($contents)
            ->toContain('South = "South"')
            ->and($contents)
            ->toContain("export enum {$name} {");
    },
);

test(
    "enums are discovered from directories configured in the config file",
    function () {
        // Set config to point at test Enums directory (instead of app_path()).
        config()->set('laravel-frontend-enums.discover_in', [__DIR__ . "/../Enums"]);

        $all = PublishEnums::get();

        // Should discover Sizes and Statuses via #[PublishEnum] attribute.
        expect($all)->toContain(Sizes::class);
        expect($all)->toContain(Statuses::class);
    },
);

test(
    "glob patterns in discover_in config are resolved to directories",
    function () {
        // Use a glob pattern that matches the test Enums directory.
        config()->set('laravel-frontend-enums.discover_in', [__DIR__ . "/../Enu*"]);

        $all = PublishEnums::get();

        // Should still discover enums from the matched directory.
        expect($all)->toContain(Sizes::class);
        expect($all)->toContain(Statuses::class);
    },
);

test(
    "per-attribute asTypescript overrides global as_typescript config",
    function () {
        $path = getcwd() . "/tests/Publish";

        // Enable TypeScript globally via config.
        config()->set('laravel-frontend-enums.as_typescript', true);
        PublishEnums::setPublishPath($path);

        $this->artisan("publish:enums-to-javascript");

        // Sizes has #[PublishEnum] (asTypescript not set) -> should follow config -> .ts
        $sizesName = new ReflectionClass(Sizes::class)->getShortName();
        $this->assertFileExists("{$path}/{$sizesName}.ts");

        // Statuses has #[PublishEnum(asTypescript: true)] -> explicitly true -> .ts
        $statusesName = new ReflectionClass(Statuses::class)->getShortName();
        $this->assertFileExists("{$path}/{$statusesName}.ts");
    },
);

test(
    "per-attribute asTypescript false overrides global as_typescript true",
    function () {
        // Sizes has #[PublishEnum] with no explicit asTypescript (null) -> follows config.
        // Statuses has #[PublishEnum(asTypescript: true)] -> explicit override.
        config()->set('laravel-frontend-enums.as_typescript', false);

        expect(PublishEnums::isTypescript(Sizes::class))->toBeFalse();
        expect(PublishEnums::isTypescript(Statuses::class))->toBeTrue();

        config()->set('laravel-frontend-enums.as_typescript', true);

        // Sizes has no explicit override, so it follows the config.
        expect(PublishEnums::isTypescript(Sizes::class))->toBeTrue();
        // Statuses still true (explicit override).
        expect(PublishEnums::isTypescript(Statuses::class))->toBeTrue();
    },
);
