<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

/**
 * The testing handler for the case Two.
 */
final class TwoHandler
{
    /**
     * The logic to run.
     */
    public function __invoke(int $a, int $b): int
    {
        return $a + $b + 120;
    }
}
