<?php

use Cerbero\LaravelEnum\SessionKeys;
use Illuminate\Support\Facades\Session;

it('supports the exists() method', function() {
    Session::shouldReceive('exists')->with('PageViews')->andReturn(true);

    expect(SessionKeys::PageViews->exists())->toBe(true);
});

it('supports the missing() method', function() {
    Session::shouldReceive('missing')->with('PageViews')->andReturn(true);

    expect(SessionKeys::PageViews->missing())->toBe(true);
});

it('supports the has() method', function() {
    Session::shouldReceive('has')->with('PageViews')->andReturn(true);

    expect(SessionKeys::PageViews->hasValue())->toBe(true);
});

it('supports the get() method', function() {
    Session::shouldReceive('get')->with('PageViews', 123)->andReturn('foo');

    expect(SessionKeys::PageViews->get(123))->toBe('foo');
});

it('supports the pull() method', function() {
    Session::shouldReceive('pull')->with('PageViews', 123)->andReturn('foo');

    expect(SessionKeys::PageViews->pull(123))->toBe('foo');
});

it('supports the hasOldInput() method', function() {
    Session::shouldReceive('hasOldInput')->with('PageViews')->andReturn(true);

    expect(SessionKeys::PageViews->hasOldInput())->toBe(true);
});

it('supports the getOldInput() method', function() {
    Session::shouldReceive('getOldInput')->with('PageViews', 123)->andReturn('foo');

    expect(SessionKeys::PageViews->getOldInput(123))->toBe('foo');
});

it('supports the put() method', function() {
    Session::shouldReceive('put')->with('PageViews', 123);

    expect(SessionKeys::PageViews->put(123))->toBe(SessionKeys::PageViews);
});

it('supports the remember() method', function() {
    $expectedCallback = Mockery::on(fn(Closure $callback) => $callback() === 'foo');

    Session::shouldReceive('remember')->with('PageViews', $expectedCallback)->andReturn('foo');

    expect(SessionKeys::PageViews->remember(fn() => 'foo'))->toBe('foo');
});

it('supports the push() method', function() {
    Session::shouldReceive('push')->with('PageViews', 123);

    expect(SessionKeys::PageViews->push(123))->toBe(SessionKeys::PageViews);
});

it('supports the increment() method', function() {
    Session::shouldReceive('increment')->with('PageViews', 2)->andReturn(10);

    expect(SessionKeys::PageViews->increment(2))->toBe(10);
});

it('supports the decrement() method', function() {
    Session::shouldReceive('decrement')->with('PageViews', 2)->andReturn(10);

    expect(SessionKeys::PageViews->decrement(2))->toBe(10);
});

it('supports the flash() method', function() {
    Session::shouldReceive('flash')->with('PageViews', 123);

    expect(SessionKeys::PageViews->flash(123))->toBe(SessionKeys::PageViews);
});

it('supports the now() method', function() {
    Session::shouldReceive('now')->with('PageViews', 123);

    expect(SessionKeys::PageViews->now(123))->toBe(SessionKeys::PageViews);
});

it('supports the remove() method', function() {
    Session::shouldReceive('remove')->with('PageViews')->andReturn('foo');

    expect(SessionKeys::PageViews->remove())->toBe('foo');
});

it('supports the forget() method', function() {
    Session::shouldReceive('forget')->with('PageViews');

    expect(SessionKeys::PageViews->forget())->toBe(SessionKeys::PageViews);
});
