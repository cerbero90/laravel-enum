<?php

namespace Cerbero\LaravelEnum;

use Closure;

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
     * @param \Cerbero\LaravelEnum\Keys|null $keys
     * @return array
     */
    public function parseDefinition(string $definition, Keys $keys = null) : array
    {
        $resolveKeys = optional($keys)->value();
        $enums = explode(static::SEPARATOR_ENUM, $definition);

        return array_map(function (string $enum) use ($resolveKeys) {
            $parts = explode(static::SEPARATOR_PART, $enum);

            return $this->hydrateEnumDefinition($parts, $resolveKeys);
        }, $enums);
    }

    /**
     * Retrieve the hydrated enum definition
     *
     * @param array $parts
     * @param \Closure|null $resolveKeys
     * @return EnumDefinition
     */
    private function hydrateEnumDefinition(array $parts, Closure $resolveKeys = null) : EnumDefinition
    {
        $enum = new EnumDefinition;
        $enum->name = $parts[0];

        if ($resolveKeys) {
            $enum->key = $resolveKeys($parts[0]);
            $enum->value = isset($parts[2]) ? $this->parseValue($parts[2]) : $this->parseValue($parts[1] ?? null);
        } else {
            $enum->key = isset($parts[1]) ? $this->parseValue($parts[1]) : Keys::LOWER()->value()($parts[0]);
            $enum->value = $this->parseValue($parts[2] ?? null);
        }

        return $enum;
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
