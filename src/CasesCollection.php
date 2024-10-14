<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Cerbero\Enum\CasesCollection as BaseCasesCollection;
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
 */
class CasesCollection extends BaseCasesCollection implements Arrayable, Jsonable, JsonSerializable, Stringable
{
    use Conditionable;
    use Macroable;
    use Tappable;

    /**
     * Turn the collection into a string.
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Turn the collection into a JSON.
     *
     * @param int $options
     */
    public function toJson($options = 0): string|false
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Turn the collection into a JSON serializable array.
     *
     * @return array<TKey, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
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
