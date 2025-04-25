<?php

namespace Intrfce\LaravelFrontendEnums\Console\Commands;

use Illuminate\Console\Command;
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

                foreach ($enumClass::cases() as $enum) {
                    $caseList[] = match ($registry->asTypescript) {
                        true => str_repeat(' ', 4) . "{$enum->name} = " . $this->printValueAsJs($enum),
                        false => str_repeat(' ', 4) . "{$enum->name}: " . $this->printValueAsJs($enum),
                    };
                }

                $name = (new ReflectionClass($enumClass))->getShortName();

                $jsFileContent = match ($registry->asTypescript) {
                    true => collect([
                        "export enum {$name} {",
                        collect($caseList)->implode(',' . PHP_EOL, ''),
                        '}',
                    ])->implode(PHP_EOL),
                    false => collect([
                        "export const {$name} = {",
                        collect($caseList)->implode(',' . PHP_EOL, ''),
                        '};',
                    ])->implode(PHP_EOL)
                };

                $extension = $registry->asTypescript ? '.ts' : '.enum.js';

                $jsFilePath = app('publish_enums_registry')->publishPath . "/{$name}{$extension}";

                file_put_contents($jsFilePath, $jsFileContent);

                if (! $this->option('compact')) {
                    info("Published: {$jsFilePath}");
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
        return match (gettype($enum->value)) {
            'string' => '"' . $enum->value . '"',
            'integer', 'double' => $enum->value,
            'boolean' => $enum->value ? 'true' : 'false',
        };
    }
}
