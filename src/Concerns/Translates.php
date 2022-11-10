<?php

namespace Cerbero\LaravelEnum\Concerns;

use Illuminate\Support\Facades\Lang;
use ValueError;

/**
 * The trait to translate enum keys.
 *
 */
trait Translates
{
    /**
     * Retrieve the translated key
     *
     * @param string $name
     * @param array $parameters
     * @return string
     * @throws ValueError
     */
    public function __call(string $name, array $parameters): string
    {
        $translation = Lang::get(sprintf('enums.%s.%s.%s', static::class, $this->name, $name), ...$parameters);

        if ($translation === $name) {
            throw new ValueError(sprintf('"%s" is not a valid key for enum "%s"', $name, static::class));
        }

        return $translation;
    }
}
