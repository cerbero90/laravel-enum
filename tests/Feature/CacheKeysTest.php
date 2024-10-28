<?php

use Cerbero\LaravelEnum\CacheKeys;
use Illuminate\Support\Facades\Cache;

it('supports the exists() method', function() {
    Cache::shouldReceive('has')->with('users.123.pinned_posts')->andReturn(true);

    expect(CacheKeys::PinnedPosts(123)->exists())->toBe(true);
});

it('supports the missing() method', function() {
    Cache::shouldReceive('missing')->with('users.123.pinned_posts')->andReturn(true);

    expect(CacheKeys::PinnedPosts(123)->missing())->toBe(true);
});

it('supports the hasValue() method', function() {
    Cache::shouldReceive('has')->with('users.123.pinned_posts')->andReturn(true);

    expect(CacheKeys::PinnedPosts(123)->hasValue())->toBe(true);
});

it('supports the get() method', function() {
    Cache::shouldReceive('get')->with('users.123.pinned_posts', 456)->andReturn('foo');

    expect(CacheKeys::PinnedPosts(123)->get(456))->toBe('foo');
});

it('supports the pull() method', function() {
    Cache::shouldReceive('pull')->with('users.123.pinned_posts', 456)->andReturn('foo');

    expect(CacheKeys::PinnedPosts(123)->pull(456))->toBe('foo');
});

it('supports the put() method', function() {
    Cache::shouldReceive('put')->with('users.123.pinned_posts', 456, 111)->andReturn(true);

    expect(CacheKeys::PinnedPosts(123)->put(456, 111))->toBe(true);
});

it('supports the add() method', function() {
    Cache::shouldReceive('add')->with('users.123.pinned_posts', 456, 111)->andReturn(true);

    expect(CacheKeys::PinnedPosts(123)->add(456, 111))->toBe(true);
});

it('supports the increment() method', function() {
    Cache::shouldReceive('increment')->with('users.123.pinned_posts', 2)->andReturn(10);

    expect(CacheKeys::PinnedPosts(123)->increment(2))->toBe(10);
});

it('supports the decrement() method', function() {
    Cache::shouldReceive('decrement')->with('users.123.pinned_posts', 2)->andReturn(10);

    expect(CacheKeys::PinnedPosts(123)->decrement(2))->toBe(10);
});

it('supports the forever() method', function() {
    Cache::shouldReceive('forever')->with('users.123.pinned_posts', 456)->andReturn(true);

    expect(CacheKeys::PinnedPosts(123)->forever(456))->toBe(true);
});

it('supports the remember() method', function() {
    $expectedCallback = Mockery::on(fn(Closure $callback) => $callback() === 'foo');

    Cache::shouldReceive('remember')->with('users.123.pinned_posts', 111, $expectedCallback)->andReturn('foo');

    expect(CacheKeys::PinnedPosts(123)->remember(111, fn() => 'foo'))->toBe('foo');
});

it('supports the rememberForever() method', function() {
    $expectedCallback = Mockery::on(fn(Closure $callback) => $callback() === 'foo');

    Cache::shouldReceive('rememberForever')->with('users.123.pinned_posts', $expectedCallback)->andReturn('foo');

    expect(CacheKeys::PinnedPosts(123)->rememberForever(fn() => 'foo'))->toBe('foo');
});

it('supports the forget() method', function() {
    Cache::shouldReceive('forget')->with('users.123.pinned_posts')->andReturn(true);

    expect(CacheKeys::PinnedPosts(123)->forget())->toBe(true);
});
