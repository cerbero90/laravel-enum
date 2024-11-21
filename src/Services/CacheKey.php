<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;

/**
 * The key dealing with the Laravel cache.
 */
final class CacheKey
{
    /**
     * Instantiate the class.
     */
    public function __construct(private readonly string $key) {}

    /**
     * Determine whether the key exists in the cache.
     */
    public function exists(): bool
    {
        return Cache::has($this->key);
    }

    /**
     * Determine whether the key doesn't exist in the cache.
     */
    public function missing(): bool
    {
        return Cache::missing($this->key);
    }

    /**
     * Determine whether the key is present and not null.
     */
    public function hasValue(): bool
    {
        return Cache::has($this->key);
    }

    /**
     * Retrieve the value of the key from the cache.
     */
    public function get(mixed $default = null): mixed
    {
        return Cache::get($this->key, $default);
    }

    /**
     * Retrieve the value of the key from the cache and delete it.
     */
    public function pull(mixed $default = null): mixed
    {
        return Cache::pull($this->key, $default);
    }

    /**
     * Store the value of the key in the cache.
     */
    public function put(mixed $value, DateTimeInterface|DateInterval|int|null $ttl = null): bool
    {
        return Cache::put($this->key, $value, $ttl);
    }

    /**
     * Store the value of the key in the cache if the key does not exist.
     */
    public function add(mixed $value, DateTimeInterface|DateInterval|int|null $ttl = null): bool
    {
        return Cache::add($this->key, $value, $ttl);
    }

    /**
     * Increment the value of the key in the cache.
     */
    public function increment(float|int $value = 1): float|int|bool
    {
        /** @var float|int|bool */
        return Cache::increment($this->key, $value);
    }

    /**
     * Decrement the value of the key in the cache.
     */
    public function decrement(float|int $value = 1): float|int|bool
    {
        /** @var float|int|bool */
        return Cache::decrement($this->key, $value);
    }

    /**
     * Store the value of the key in the cache indefinitely.
     */
    public function forever(mixed $value): bool
    {
        return Cache::forever($this->key, $value);
    }

    /**
     * Retrieve or store the value of the key.
     */
    public function remember(Closure|DateTimeInterface|DateInterval|int|null $ttl, Closure $callback): mixed
    {
        return Cache::remember($this->key, $ttl, $callback);
    }

    /**
     * Retrieve or store indefinitely the value of the key.
     */
    public function rememberForever(Closure $callback): mixed
    {
        return Cache::rememberForever($this->key, $callback);
    }

    /**
     * Remove the key from the cache.
     */
    public function forget(): bool
    {
        return Cache::forget($this->key);
    }
}
