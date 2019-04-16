<?php

namespace Cerbero\LaravelEnum\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Cerbero\LaravelEnum\Parsers\Parser;
use Cerbero\LaravelEnum\Parsers\EnumDefinition;

/**
 * The Artisan command to generate Enum classes.
 *
 */
class EnumMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {name} {enum : The enum definition} {--p|path= : The path to generate enums in}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Enum';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/enum.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        return tap(parent::buildClass($name), function (&$stub) {
            $enums = $this->parseEnums();

            $this->replaceMethodTags($stub, $enums);
            $this->replaceConstants($stub, $enums);
            $this->replaceMap($stub, $enums);
        });
    }

    /**
     * Parse the command syntax and retrieve the enums
     *
     * @return array
     */
    private function parseEnums() : array
    {
        $definition = trim($this->argument('enum'));

        return (new Parser)->parseDefinition($definition);
    }

    /**
     * Replace the PHPDoc method tags for the given stub
     *
     * @param string $stub
     * @return void
     */
    private function replaceMethodTags(string &$stub, array $enums) : void
    {
        $methods = array_map(function ($enum) {
            return " * @method static self {$enum->name}()";
        }, $enums);

        $stub = str_replace('DummyMethodTags', implode(PHP_EOL, $methods), $stub);
    }

    /**
     * Replace the constants for the given stub
     *
     * @param string $stub
     * @return void
     */
    private function replaceConstants(string &$stub, array $enums) : void
    {
        $constants = array_map(function ($enum) {
            ///////////// @todo check out what happens when key is array - may need to format it
            $key = is_string($enum->key) ? "'{$enum->key}'" : $enum->key;
            return "    const {$enum->name} = {$key};";
        }, $enums);

        $stub = str_replace('DummyConstants', implode(PHP_EOL, $constants), $stub);
    }

    /**
     * Replace the map for the given stub
     *
     * @param string $stub
     * @return void
     */
    private function replaceMap(string &$stub, array $enums) : void
    {
        // Map enums key and value pairs only if enums have values
        if ($this->enumsHaveValues($enums)) {
            $mapStub = __DIR__ . '/../../../stubs/map.stub';
            $stub = str_replace('DummyMap', file_get_contents($mapStub), $stub);
            $this->replaceMapPairs($stub, $enums);
        } else {
            $stub = str_replace('DummyMap', '', $stub);
        }
    }

    /**
     * Determine whether the given enums contain values
     *
     * @param array $enums
     * @return bool
     */
    private function enumsHaveValues(array $enums) : bool
    {
        //////////// consider using Enum classes instead of EnumDefinition
        foreach ($enums as $enum) {
            if ($enum->value !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Replace the enums key and value pairs
     *
     * @param string $stub
     * @param array $enums
     * @return void
     */
    private function replaceMapPairs(string &$stub, array $enums) : void
    {
        $pairs = array_map(function ($enum) {
            return "            static::{$enum->name} => {$enum->value},";
        }, $enums);

        $stub = str_replace('DummyKeyValuePairs', implode(PHP_EOL, $pairs), $stub);
    }
}
