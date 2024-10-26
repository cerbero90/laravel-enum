<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

/**
 * The default testing handler.
 */
final class DefaultHandler
{
    /**
     * The logic to run.
     */
    public function __invoke(int $a, int $b): int
    {
        return $a + $b;
    }
}
