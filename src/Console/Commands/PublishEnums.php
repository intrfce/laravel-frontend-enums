<?php

namespace Intrfce\LaravelFrontendEnums\Console\Commands;

use Illuminate\Console\Command;
use ReflectionClass;

class PublishEnums extends Command
{

    protected $signature = 'publish:enums-to-javascript';

    protected $description = 'Publishes the listed enum classes to Javascript classes so they can be accessed easily.';

    public function handle(): void
    {

        foreach (app('publish_enums_registry')->get() as $enumClass) {

            $enumClass = trim($enumClass);

            $caseList = [];

            if (class_exists($enumClass)) {

                foreach ($enumClass::cases() as $enum) {
                    $caseList[] = str_repeat(' ', 4)."{$enum->name}: ".$this->printValueAsJs($enum);
                }

                $name = (new ReflectionClass($enumClass))->getShortName();

                $jsFileContent = collect([
                    "export const {$name} = {",
                    collect($caseList)->implode(','.PHP_EOL, ''),
                    '};',
                ])->implode(PHP_EOL);

                $jsFilePath = app('publish_enums_registry')->publishPath."/{$name}.enum.js";

                file_put_contents($jsFilePath, $jsFileContent);

                \Laravel\Prompts\info("Published: {$jsFilePath}");
            } else {
                $this->error("Enum class {$enumClass} not found.");
            }
        }
    }

    private function printValueAsJs(mixed $enum): string
    {
        return match (gettype($enum->value)) {
            'string' => '"'.$enum->value.'"',
            'integer', 'double' => $enum->value,
            'boolean' => $enum->value ? 'true' : 'false',
        };
    }
}
