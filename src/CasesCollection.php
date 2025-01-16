<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Cerbero\Enum\CasesCollection as BaseCasesCollection;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

/**
 * The collection of enum cases.
 *
 * @template-covariant TEnum of \UnitEnum
 *
 * @extends BaseCasesCollection<TEnum>
 * @implements Arrayable<array-key, TEnum>
 */
class CasesCollection extends BaseCasesCollection implements Arrayable, Castable, Jsonable
{
    use Conditionable;
    use Macroable;
    use Tappable;

    /**
     * Retrieve the caster to cast the collection.
     *
     * @param list<string> $arguments
     * @return CasesCollectionCast<TEnum>
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        /** @var CasesCollectionCast<TEnum> */
        return new CasesCollectionCast($arguments[0] ?? '');
    }

    /**
     * Retrieve the cast for the given enum.
     *
     * @param class-string<TEnum> $enum
     */
    public static function of(string $enum): string
    {
        return static::class . ':' . $enum;
    }

    /**
     * Turn the collection into a JSON.
     *
     * @param int $options
     */
    public function toJson($options = 0): string|false
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Dump the cases and end the script.
     *
     * @codeCoverageIgnore
     */
    public function dd(): never
    {
        $this->dump();

        exit(1);
    }

    /**
     * Dump the cases.
     */
    public function dump(): static
    {
        dump($this->cases);

        return $this;
    }
}
