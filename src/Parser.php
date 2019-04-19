<?php

namespace Cerbero\LaravelEnum;

use Illuminate\Support\Str;

/**
 * The enums parser.
 *
 */
class Parser
{
    const SEPARATOR_ENUM = '|';
    const SEPARATOR_PART = '=';

    /**
     * Parse the enums definition
     *
     * @param string $definition
     * @return array
     */
    public function parseDefinition(string $definition) : array
    {
        $enums = explode(static::SEPARATOR_ENUM, $definition);

        return array_map(function (string $enum) {
            $parts = explode(static::SEPARATOR_PART, $enum);

            return $this->hydrateEnumDefinition($parts);
        }, $enums);
    }

    /**
     * Retrieve the hydrated enum definition
     *
     * @param array $parts
     * @return EnumDefinition
     */
    private function hydrateEnumDefinition(array $parts) : EnumDefinition
    {
        return tap(new EnumDefinition, function ($enumDefinition) use ($parts) {
            $enumDefinition->name = $parts[0];
            $enumDefinition->key = isset($parts[1]) ? $this->parseValue($parts[1]) : Str::lower($parts[0]);
            $enumDefinition->value = isset($parts[2]) ? $this->parseValue($parts[2]) : null;
        });
    }

    /**
     * Parse the given variable to retrieve its actual value
     *
     * @param mixed $value
     * @return mixed
     */
    public function parseValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Return floats, integers, booleans or arrays if possible
        if (null !== $decoded = json_decode($value, true)) {
            return $decoded;
        }

        return $value;
    }
}
