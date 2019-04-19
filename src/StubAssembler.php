<?php

namespace Cerbero\LaravelEnum;

/**
 * The stub assembler.
 *
 */
class StubAssembler
{
    use ExportsValues;

    /**
     * The stub to assemble.
     *
     * @var string
     */
    protected $stub;

    /**
     * The list of enum definitions.
     *
     * @var array
     */
    protected $enums;

    /**
     * Set the dependencies.
     *
     * @param string $stub
     * @param array $enums
     */
    public function __construct(string $stub, array $enums)
    {
        $this->stub = $stub;
        $this->enums = $enums;
    }

    /**
     * Retrieve the stub
     *
     * @return string
     */
    public function getStub() : string
    {
        return $this->stub;
    }

    /**
     * Replace the PHPDoc method tags for the given stub
     *
     * @return self
     */
    public function replaceMethodTags() : self
    {
        $methods = array_map(function (EnumDefinition $enum) {
            return " * @method static self {$enum->name}()";
        }, $this->enums);

        $this->stub = str_replace('DummyMethodTags', implode(PHP_EOL, $methods), $this->stub);

        return $this;
    }

    /**
     * Replace the constants for the given stub
     *
     * @return self
     */
    public function replaceConstants() : self
    {
        $padding = 4;

        $constants = array_map(function (EnumDefinition $enum) use ($padding) {
            $key = $this->export($enum->key, $padding);
            return str_repeat(' ', $padding) . "const {$enum->name} = {$key};";
        }, $this->enums);

        $this->stub = str_replace('DummyConstants', implode(PHP_EOL, $constants), $this->stub);

        return $this;
    }

    /**
     * Replace the map for the given stub
     *
     * @return self
     */
    public function replaceMap() : self
    {
        // Map enums key and value pairs only if enums have values
        if ($this->enumsHaveValues()) {
            $mapStub = file_get_contents(__DIR__ . '/../stubs/map.stub');
            $this->stub = str_replace('DummyMap', $mapStub, $this->stub);
            $this->replaceMapPairs();
        } else {
            $this->stub = str_replace('DummyMap', '', $this->stub);
        }

        return $this;
    }

    /**
     * Determine whether the given enums contain values
     *
     * @return bool
     */
    private function enumsHaveValues() : bool
    {
        foreach ($this->enums as $enum) {
            if ($enum->value !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Replace the enums key and value pairs
     *
     * @return self
     */
    public function replaceMapPairs() : self
    {
        $padding = 12;

        $pairs = array_map(function (EnumDefinition $enum) use ($padding) {
            $value = $this->export($enum->value, $padding);
            return str_repeat(' ', $padding) . "static::{$enum->name} => {$value},";
        }, $this->enums);

        $this->stub = str_replace('DummyKeyValuePairs', implode(PHP_EOL, $pairs), $this->stub);

        return $this;
    }
}
