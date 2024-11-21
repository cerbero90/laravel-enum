<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Generator;

/**
 * Determine whether the given namespace exists.
 */
function namespaceExists(string $target): bool
{
    return class_exists($target) || interface_exists($target);
}

/**
 * Yield the content of the given path line by line.
 *
 * @return Generator<int, string>
 */
function yieldLines(string $path): Generator
{
    $stream = fopen($path, 'rb');

    try {
        while (($line = fgets($stream, 1024)) !== false) {
            yield $line;
        }
    } finally {
        is_resource($stream) && fclose($stream);
    }
}

/**
 * Retrieve the common type among the given types.
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
