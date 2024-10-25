<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use BackedEnum;
use Cerbero\LaravelEnum\Contracts\Bitwise;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use UnitEnum;

/**
 * The cases collection cast.
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @implements CastsAttributes<CasesCollection, mixed>
 */
class CasesCollectionCast implements CastsAttributes
{
    /**
     * Disable object caching.
     */
    public bool $withoutObjectCaching = true;

    /**
     * Instantiate the class.
     */
    public function __construct(private readonly string $enum)
    {
        if (! is_subclass_of($enum, UnitEnum::class)) {
            throw new InvalidArgumentException('The cast argument must be a valid enum');
        }
    }

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param string|int|null $value
     * @param array<string, mixed> $attributes
     * @return CasesCollection<TKey, TValue>
     * @throws \ValueError
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?CasesCollection
    {
        return match (true) {
            is_string($value) => $this->getByJson($value),
            /** @phpstan-ignore-next-line binaryOp.invalid */
            is_int($value) => $this->enum::filter(fn(BackedEnum $case) => ($value & $case->value) == $case->value),
            default => null,
        };
    }

    /**
     * Transform the given JSON into a cases collection.
     *
     * @return CasesCollection<TKey, TValue>
     * @throws \ValueError
     */
    protected function getByJson(string $json): CasesCollection
    {
        /** @var list<string|int> $cases */
        $cases = json_decode($json, true);
        $cases = array_unique($cases);

        /** @var CasesCollection<TKey, TValue> */
        return new CasesCollection(array_map(fn(string|int $value) => $this->enum::from($value), $cases));
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string|int|null
    {
        $this->withoutObjectCaching = ! $value instanceof CasesCollection;

        return match (true) {
            $value instanceof CasesCollection => $value->toJson() ?: null,
            is_array($value) => $this->setByArray($value),
            is_int($value) => $value,
            default => null,
        };
    }

    /**
     * Transform the given array into a serializable string.
     *
     * @param array<array-key, mixed> $array
     */
    protected function setByArray(array $array): string|int|null
    {
        if (is_subclass_of($this->enum, Bitwise::class)) {
            return array_reduce($array, function (?int $carry, mixed $item): int {
                return $carry |= $item instanceof BackedEnum ? $item->value : $item;
            });
        }

        $values = reset($array) instanceof UnitEnum
            ? $this->enum::only(...array_column($array, 'name'))
            : array_values(array_unique($array));

        return json_encode($values) ?: null;
    }
}
