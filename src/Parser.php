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

    private $key = 1;

    /**
     * Parse the enums definition
     *
     * @param string $definition
     * @param bool $numeric
     * @param bool $bitwise
     * @return array
     */
    public function parseDefinition(string $definition, bool $numeric = false, bool $bitwise = false) : array
    {
        $enums = explode(static::SEPARATOR_ENUM, $definition);

        return array_map(function (string $enum) use ($numeric, $bitwise) {
            $parts = explode(static::SEPARATOR_PART, $enum);

            if ($numeric) {
                $parts = $this->addNumericKey($parts);
            } elseif ($bitwise) {
                $parts = $this->addBitwiseKey($parts);
            }

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
     * Add a numeric enum key to the parts.
     *
     * @param  array  $parts
     * @return array
     */
    private function addNumericKey(array $parts)
    {
        array_splice($parts, 1, 0, $this->key);
        $this->key++;

        return $parts;
    }

    /**
     * Add a bitwise enum key to the parts.
     *
     * @param  array  $parts
     * @return array
     */
    private function addBitwiseKey(array $parts)
    {
        array_splice($parts, 1, 0, $this->key);
        $this->key *= 2;

        return $parts;
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
