<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Capsules;

use Closure;
use Illuminate\Support\Facades\Session;

/**
 * The key dealing with the Laravel session.
 */
final class SessionKey
{
    /**
     * Instantiate the class.
     */
    public function __construct(private readonly string $key) {}

    /**
     * Determine whether the key exists.
     */
    public function exists(): bool
    {
        return Session::exists($this->key);
    }

    /**
     * Determine whether the key is missing from the session.
     */
    public function missing(): bool
    {
        return Session::missing($this->key);
    }

    /**
     * Determine whether the key is present and not null.
     */
    public function hasValue(): bool
    {
        return Session::has($this->key);
    }

    /**
     * Retrieve the value of the key from the session.
     */
    public function get(mixed $default = null): mixed
    {
        return Session::get($this->key, $default);
    }

    /**
     * Retrieve the value of the key and then forget it.
     */
    public function pull(mixed $default = null): mixed
    {
        return Session::pull($this->key, $default);
    }

    /**
     * Determine whether the session contains old input for the key.
     */
    public function hasOldInput(): bool
    {
        return Session::hasOldInput($this->key);
    }

    /**
     * Retrieve the value from the flashed input.
     */
    public function getOldInput(mixed $default = null): mixed
    {
        return Session::getOldInput($this->key, $default);
    }

    /**
     * Put the value of the key in the session.
     */
    public function put(mixed $value = null): self
    {
        Session::put($this->key, $value);

        return $this;
    }

    /**
     * Retrieve or store the value of the key.
     */
    public function remember(Closure $callback): mixed
    {
        return Session::remember($this->key, $callback);
    }

    /**
     * Push a value onto the array of the key.
     */
    public function push(mixed $value): self
    {
        Session::push($this->key, $value);

        return $this;
    }

    /**
     * Increment the value of the key.
     */
    public function increment(float|int $amount = 1): float|int
    {
        return Session::increment($this->key, $amount);
    }

    /**
     * Decrement the value of the key.
     */
    public function decrement(float|int $amount = 1): float|int
    {
        return Session::decrement($this->key, $amount);
    }

    /**
     * Flash the value of the key to the session.
     */
    public function flash(mixed $value = true): self
    {
        Session::flash($this->key, $value);

        return $this;
    }

    /**
     * Flash the value of the key to the session for immediate use.
     */
    public function now(mixed $value): self
    {
        Session::now($this->key, $value);

        return $this;
    }

    /**
     * Remove the key from the session and retrieve its value.
     */
    public function remove(): mixed
    {
        return Session::remove($this->key);
    }

    /**
     * Remove the key from the session.
     */
    public function forget(): self
    {
        Session::forget($this->key);

        return $this;
    }
}
