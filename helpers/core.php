<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use UnitEnum;

/**
 * Retrieve the type in common with the given types.
 */
function commonType(string ...$types): string
{
    $null = '';
    $types = array_unique($types);

    if (($index = array_search('null', $types)) !== false) {
        $null = '?';

        unset($types[$index]);
    }

    return match (true) {
        ! is_null($common = commonInterfaceOrParent(...$types)) => $null . $common,
        count($types) == 1 => $null . reset($types),
        default => implode('|', $types) . ($null ? '|null' : ''),
    };
}

/**
 * Retrieve the first interface or parent class in common with the given classes.
 *
 * @return ?class-string
 */
function commonInterfaceOrParent(string ...$classes): ?string
{
    if ($classes !== array_filter($classes, namespaceExists(...))) {
        return null;
    }

    foreach (['class_implements', 'class_parents'] as $callback) {
        /** @var array<class-string, class-string> $common */
        if ($common = array_intersect(...array_map($callback, $classes))) {
            return reset($common);
        }
    }

    return null;
}

/**
 * Determine whether the given namespace exists.
 */
function namespaceExists(string $namespace): bool
{
    return class_exists($namespace) || interface_exists($namespace);
}

/**
 * Retrieve the return type of the given meta for the provided cases.
 *
 * @param list<UnitEnum> $cases
 */
function metaReturnType(string $meta, array $cases): string
{
    $returnTypes = array_map(function (UnitEnum $case) use ($meta) {
        $value = $case->resolveMetaAttribute($meta);

        return is_string($value) && namespaceExists($value) ? $value : get_debug_type($value);
    }, $cases);

    return commonType(...$returnTypes);
}
