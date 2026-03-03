<?php

namespace Intrfce\LaravelFrontendEnums\Console\Commands;

use BackedEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;

use function Laravel\Prompts\info;

class PublishEnumsCommand extends Command
{
    protected $signature = 'publish:enums-to-javascript {--compact}';

    protected $description = 'Publishes the listed enum classes to Javascript classes so they can be accessed easily.';

    public function handle(): void
    {
        $registry = app('publish_enums_registry');
        $published = 0;
        foreach ($registry->get() as $enumClass) {
            $enumClass = trim($enumClass);

            $caseList = [];

            if (class_exists($enumClass)) {
                $useTypescript = $registry->isTypescript($enumClass);

                foreach ($enumClass::cases() as $enum) {
                    $caseList[] = match ($useTypescript) {
                        true => str_repeat(' ', 4) .
                            "{$enum->name} = " .
                            $this->printValueAsJs($enum),
                        false => str_repeat(' ', 4) .
                            "{$enum->name}: " .
                            $this->printValueAsJs($enum),
                    };
                }

                $name = new ReflectionClass($enumClass)->getShortName();

                $jsFileContent = match ($useTypescript) {
                    true => collect([
                        "export enum {$name} {",
                        collect($caseList)->implode(',' . PHP_EOL, ''),
                        '} as const;',
                    ])->implode(PHP_EOL),
                    false => collect([
                        "export const {$name} = {",
                        collect($caseList)->implode(',' . PHP_EOL, ''),
                        '};',
                    ])->implode(PHP_EOL),
                };

                $extension = $useTypescript ? '.ts' : '.enum.js';

                $jsFilePath = app('publish_enums_registry')->publishPath;

                $jsFilePathAndName = $jsFilePath . "/{$name}{$extension}";

                File::ensureDirectoryExists($jsFilePath);

                File::put($jsFilePathAndName, $jsFileContent);

                if (! $this->option('compact')) {
                    info("Published: {$jsFilePathAndName}");
                }

                $published++;
            } else {
                $this->error("Enum class {$enumClass} not found.");
            }
        }

        if ($this->option('compact')) {
            info("Published {$published} files.");
        }
    }

    private function printValueAsJs(mixed $enum): string
    {
        if (! $enum instanceof BackedEnum) {
            return '"' . $enum->name . '"';
        }

        return match (gettype($enum->value)) {
            'string' => '"' . $enum->value . '"',
            'integer', 'double' => $enum->value,
            'boolean' => $enum->value ? 'true' : 'false',
        };
    }
}
