<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Concerns;

use Closure;
use Illuminate\Support\Facades\Session;

/**
 * The trait to enumerate session keys.
 */
trait EnumeratesSessionKeys
{
    use Enumerates;

    /**
     * Determine whether the key exists.
     */
    public function exists(): bool
    {
        return Session::exists($this->value());
    }

    /**
     * Determine whether the key is missing from the session.
     */
    public function missing(): bool
    {
        return Session::missing($this->value());
    }

    /**
     * Determine whether the key is present and not null.
     */
    public function hasValue(): bool
    {
        return Session::has($this->value());
    }

    /**
     * Retrieve the value of the key from the session.
     */
    public function get(mixed $default = null): mixed
    {
        return Session::get($this->value(), $default);
    }

    /**
     * Retrieve the value of the key and then forget it.
     */
    public function pull(mixed $default = null): mixed
    {
        return Session::pull($this->value(), $default);
    }

    /**
     * Determine whether the session contains old input for the key.
     */
    public function hasOldInput(): bool
    {
        return Session::hasOldInput($this->value());
    }

    /**
     * Retrieve the value from the flashed input.
     */
    public function getOldInput(mixed $default = null): mixed
    {
        return Session::getOldInput($this->value(), $default);
    }

    /**
     * Put the value of the key in the session.
     */
    public function put(mixed $value = null): self
    {
        Session::put($this->value(), $value);

        return $this;
    }

    /**
     * Retrieve or store the value of the key.
     */
    public function remember(Closure $callback): mixed
    {
        return Session::remember($this->value(), $callback);
    }

    /**
     * Push a value onto the array of the key.
     */
    public function push(mixed $value): self
    {
        Session::push($this->value(), $value);

        return $this;
    }

    /**
     * Increment the value of the key.
     */
    public function increment(float|int $amount = 1): float|int
    {
        return Session::increment($this->value(), $amount);
    }

    /**
     * Decrement the value of the key.
     */
    public function decrement(float|int $amount = 1): float|int
    {
        return Session::decrement($this->value(), $amount);
    }

    /**
     * Flash the value of the key to the session.
     */
    public function flash(mixed $value = true): self
    {
        Session::flash($this->value(), $value);

        return $this;
    }

    /**
     * Flash the value of the key to the session for immediate use.
     */
    public function now(mixed $value): self
    {
        Session::now($this->value(), $value);

        return $this;
    }

    /**
     * Remove the key from the session and retrieve its value.
     */
    public function remove(): mixed
    {
        return Session::remove($this->value());
    }

    /**
     * Remove the key from the session.
     */
    public function forget(): self
    {
        Session::forget($this->value());

        return $this;
    }
}
