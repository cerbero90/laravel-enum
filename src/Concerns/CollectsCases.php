<?php

namespace Cerbero\LaravelEnum\Concerns;

use Cerbero\Enum\Concerns\CollectsCases as BaseCollectsCases;
use Cerbero\LaravelEnum\CasesCollection;

/**
 * The trait to collect the cases of an enum.
 */
trait CollectsCases
{
    use BaseCollectsCases;

    /**
     * Retrieve a collection with all the cases.
     *
     * @return CasesCollection<static>
     */
    public static function collect(): CasesCollection
    {
        return new CasesCollection(self::cases());
    }
}
