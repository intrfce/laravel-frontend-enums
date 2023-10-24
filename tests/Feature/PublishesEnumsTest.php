<?php

use Illuminate\Support\Facades\Storage;
use Intrfce\LaravelFrontendEnums\Exceptions\ArgumentIsNotEnumException;

afterEach(function() {
    collect(\Illuminate\Support\Facades\File::allFiles('tests/Publish'))->reject(function($file) {
        return str_contains('.gitkeep', $file->getFilename());
    })->each(fn ($file) => \Illuminate\Support\Facades\File::delete($file->getPathname()));
});

test("The package registry picks up on the enums you list", function() {
    $enumsToPublish = app('publish_enums_registry')->get();
    expect($enumsToPublish)->toHaveCount(2);
//    \Artisan::call('publish:enums-to-javascript');
});

test("Listing anything other than an enum produces an exception", function($test) {

    \Intrfce\LaravelFrontendEnums\Facades\PublishEnums::publish([
       $test
    ]);

})
    ->with([
        'not an enum',
        3.141,
        23141,
    ])
    ->throws(ArgumentIsNotEnumException::class);


test("It publishes the enums to the default director at resources/js/Enums", function() {
    $path = getcwd().'/tests/Publish';
    \Intrfce\LaravelFrontendEnums\Facades\PublishEnums::setPublishPath($path);
    $this->artisan('publish:enums-to-javascript');

    collect(\Intrfce\LaravelFrontendEnums\Facades\PublishEnums::get())->each(function($enum) use ($path) {
        $name = (new ReflectionClass($enum))->getShortName();
        $this->assertFileExists("{$path}/{$name}.enum.js");
        $contents = file_get_contents("{$path}/{$name}.enum.js");
        collect($enum::cases())->each(fn ($case) => expect($contents)->toContain($case->name)
            ->and($contents)->toContain((string)$case->value)
            ->and($contents)->toContain("export const {$name} = {"));
    });
});