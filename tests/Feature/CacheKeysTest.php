<?php

use Cerbero\LaravelEnum\CacheKeys;
use Illuminate\Support\Facades\Cache;

it('supports the exists() method', function() {
    Cache::shouldReceive('has')->with('teams.123.users.abc.pinned_posts')->andReturn(true);

    expect(CacheKeys::PinnedPosts(123, 'abc')->exists())->toBe(true);
});

it('supports the missing() method', function() {
    Cache::shouldReceive('missing')->with('teams.123.users.abc.pinned_posts')->andReturn(true);

    expect(CacheKeys::PinnedPosts(123, 'abc')->missing())->toBe(true);
});

it('supports the hasValue() method', function() {
    Cache::shouldReceive('has')->with('teams.123.users.abc.pinned_posts')->andReturn(true);

    expect(CacheKeys::PinnedPosts(123, 'abc')->hasValue())->toBe(true);
});

it('supports the get() method', function() {
    Cache::shouldReceive('get')->with('teams.123.users.abc.pinned_posts', 456)->andReturn('foo');

    expect(CacheKeys::PinnedPosts(123, 'abc')->get(456))->toBe('foo');
});

it('supports the pull() method', function() {
    Cache::shouldReceive('pull')->with('teams.123.users.abc.pinned_posts', 456)->andReturn('foo');

    expect(CacheKeys::PinnedPosts(123, 'abc')->pull(456))->toBe('foo');
});

it('supports the put() method', function() {
    Cache::shouldReceive('put')->with('teams.123.users.abc.pinned_posts', 456, 111)->andReturn(true);

    expect(CacheKeys::PinnedPosts(123, 'abc')->put(456, 111))->toBe(true);
});

it('supports the add() method', function() {
    Cache::shouldReceive('add')->with('teams.123.users.abc.pinned_posts', 456, 111)->andReturn(true);

    expect(CacheKeys::PinnedPosts(123, 'abc')->add(456, 111))->toBe(true);
});

it('supports the increment() method', function() {
    Cache::shouldReceive('increment')->with('teams.123.users.abc.pinned_posts', 2)->andReturn(10);

    expect(CacheKeys::PinnedPosts(123, 'abc')->increment(2))->toBe(10);
});

it('supports the decrement() method', function() {
    Cache::shouldReceive('decrement')->with('teams.123.users.abc.pinned_posts', 2)->andReturn(10);

    expect(CacheKeys::PinnedPosts(123, 'abc')->decrement(2))->toBe(10);
});

it('supports the forever() method', function() {
    Cache::shouldReceive('forever')->with('teams.123.users.abc.pinned_posts', 456)->andReturn(true);

    expect(CacheKeys::PinnedPosts(123, 'abc')->forever(456))->toBe(true);
});

it('supports the remember() method', function() {
    $expectedCallback = Mockery::on(fn(Closure $callback) => $callback() === 'foo');

    Cache::shouldReceive('remember')->with('teams.123.users.abc.pinned_posts', 111, $expectedCallback)->andReturn('foo');

    expect(CacheKeys::PinnedPosts(123, 'abc')->remember(111, fn() => 'foo'))->toBe('foo');
});

it('supports the rememberForever() method', function() {
    $expectedCallback = Mockery::on(fn(Closure $callback) => $callback() === 'foo');

    Cache::shouldReceive('rememberForever')->with('teams.123.users.abc.pinned_posts', $expectedCallback)->andReturn('foo');

    expect(CacheKeys::PinnedPosts(123, 'abc')->rememberForever(fn() => 'foo'))->toBe('foo');
});

it('supports the forget() method', function() {
    Cache::shouldReceive('forget')->with('teams.123.users.abc.pinned_posts')->andReturn(true);

    expect(CacheKeys::PinnedPosts(123, 'abc')->forget())->toBe(true);
});
