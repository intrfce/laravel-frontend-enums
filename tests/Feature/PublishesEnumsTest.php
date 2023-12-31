<?php

use Illuminate\Support\Facades\File;
use Intrfce\LaravelFrontendEnums\Exceptions\ArgumentIsNotEnumException;
use Intrfce\LaravelFrontendEnums\Facades\PublishEnums;

afterEach(function () {
    collect(File::allFiles('tests/Publish'))->reject(function ($file) {
        return str_contains('.gitkeep', $file->getFilename());
    })->each(fn ($file) => File::delete($file->getPathname()));
});

test('The package registry picks up on the enums you list', function () {
    $enumsToPublish = app('publish_enums_registry')->get();
    expect($enumsToPublish)->toHaveCount(2);
    //    \Artisan::call('publish:enums-to-javascript');
});

test('Listing anything other than an enum produces an exception', function ($test) {

    PublishEnums::publish([
        $test,
    ]);

})
    ->with([
        'not an enum',
        3.141,
        23141,
    ])
    ->throws(ArgumentIsNotEnumException::class);

test('It publishes the enums to the default directory at resources/js/Enums', function () {
    $path = getcwd() . '/tests/Publish';
    PublishEnums::setPublishPath($path);
    $this->artisan('publish:enums-to-javascript');

    collect(PublishEnums::get())->each(function ($enum) use ($path) {
        $name = (new ReflectionClass($enum))->getShortName();
        $this->assertFileExists("{$path}/{$name}.enum.js");
        $contents = file_get_contents("{$path}/{$name}.enum.js");
        collect($enum::cases())->each(fn ($case) => expect($contents)->toContain($case->name)
            ->and($contents)->toContain((string) $case->value)
            ->and($contents)->toContain("export const {$name} = {"));
    });
});

test('It publishes the enums to the default directory at resources/js/Enums as typescript files.', function () {
    $path = getcwd() . '/tests/Publish';
    PublishEnums::setPublishPath($path)->asTypescript();
    $this->artisan('publish:enums-to-javascript');

    collect(PublishEnums::get())->each(function ($enum) use ($path) {
        $name = (new ReflectionClass($enum))->getShortName();
        $this->assertFileExists("{$path}/{$name}.ts");
        $contents = file_get_contents("{$path}/{$name}.ts");
        collect($enum::cases())->each(fn ($case) => expect($contents)->toContain($case->name)
            ->and($contents)->toContain((string) $case->value)
            ->and($contents)->toContain("export enum {$name} {"));
    });
});
