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
use JsonSerializable;
use Stringable;

/**
 * The collection of enum cases.
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @extends BaseCasesCollection<TKey, TValue>
 * @implements Arrayable<TKey, TValue>
 */
class CasesCollection extends BaseCasesCollection implements Arrayable, Castable, Jsonable, JsonSerializable, Stringable
{
    use Conditionable;
    use Macroable;
    use Tappable;

    /**
     * Retrieve the caster to cast the collection.
     *
     * @param string[] $arguments
     * @return CasesCollectionCast<TKey, TValue>
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new CasesCollectionCast($arguments[0] ?? '');
    }

    /**
     * Retrieve the cast for the given enum.
     *
     * @param class-string<\UnitEnum> $enum
     */
    public static function of(string $enum): string
    {
        return static::class . ':' . $enum;
    }

    /**
     * Turn the collection into a string.
     */
    public function __toString(): string
    {
        return (string) $this->toJson();
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
     * Turn the collection into a JSON serializable array.
     *
     * @return array<TKey, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->enumIsBacked ? $this->values() : $this->names();
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
