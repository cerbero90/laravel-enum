<?php

use Cerbero\LaravelEnum\Capsules\SessionKey;
use Domain\Common\Enums\SessionKeys;
use Illuminate\Support\Facades\Session;

it('supports the exists() method', function() {
    Session::shouldReceive('exists')->with('CartItems')->andReturn(true);

    expect(SessionKeys::CartItems()->exists())->toBe(true);
});

it('supports dynamic parameters', function() {
    Session::shouldReceive('exists')->with('Forms.123.Data')->andReturn(true);

    expect(SessionKeys::FormsData(123)->exists())->toBe(true);
});

it('supports the missing() method', function() {
    Session::shouldReceive('missing')->with('CartItems')->andReturn(true);

    expect(SessionKeys::CartItems()->missing())->toBe(true);
});

it('supports the has() method', function() {
    Session::shouldReceive('has')->with('CartItems')->andReturn(true);

    expect(SessionKeys::CartItems()->hasValue())->toBe(true);
});

it('supports the get() method', function() {
    Session::shouldReceive('get')->with('CartItems', 123)->andReturn('foo');

    expect(SessionKeys::CartItems()->get(123))->toBe('foo');
});

it('supports the pull() method', function() {
    Session::shouldReceive('pull')->with('CartItems', 123)->andReturn('foo');

    expect(SessionKeys::CartItems()->pull(123))->toBe('foo');
});

it('supports the hasOldInput() method', function() {
    Session::shouldReceive('hasOldInput')->with('CartItems')->andReturn(true);

    expect(SessionKeys::CartItems()->hasOldInput())->toBe(true);
});

it('supports the getOldInput() method', function() {
    Session::shouldReceive('getOldInput')->with('CartItems', 123)->andReturn('foo');

    expect(SessionKeys::CartItems()->getOldInput(123))->toBe('foo');
});

it('supports the put() method', function() {
    Session::shouldReceive('put')->with('CartItems', 123);

    expect(SessionKeys::CartItems()->put(123))->toBeInstanceOf(SessionKey::class);
});

it('supports the remember() method', function() {
    $expectedCallback = Mockery::on(fn(Closure $callback) => $callback() === 'foo');

    Session::shouldReceive('remember')->with('CartItems', $expectedCallback)->andReturn('foo');

    expect(SessionKeys::CartItems()->remember(fn() => 'foo'))->toBe('foo');
});

it('supports the push() method', function() {
    Session::shouldReceive('push')->with('CartItems', 123);

    expect(SessionKeys::CartItems()->push(123))->toBeInstanceOf(SessionKey::class);
});

it('supports the increment() method', function() {
    Session::shouldReceive('increment')->with('CartItems', 2)->andReturn(10);

    expect(SessionKeys::CartItems()->increment(2))->toBe(10);
});

it('supports the decrement() method', function() {
    Session::shouldReceive('decrement')->with('CartItems', 2)->andReturn(10);

    expect(SessionKeys::CartItems()->decrement(2))->toBe(10);
});

it('supports the flash() method', function() {
    Session::shouldReceive('flash')->with('CartItems', 123);

    expect(SessionKeys::CartItems()->flash(123))->toBeInstanceOf(SessionKey::class);
});

it('supports the now() method', function() {
    Session::shouldReceive('now')->with('CartItems', 123);

    expect(SessionKeys::CartItems()->now(123))->toBeInstanceOf(SessionKey::class);
});

it('supports the remove() method', function() {
    Session::shouldReceive('remove')->with('CartItems')->andReturn('foo');

    expect(SessionKeys::CartItems()->remove())->toBe('foo');
});

it('supports the forget() method', function() {
    Session::shouldReceive('forget')->with('CartItems');

    expect(SessionKeys::CartItems()->forget())->toBeInstanceOf(SessionKey::class);
});
