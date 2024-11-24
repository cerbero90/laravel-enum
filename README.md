# 🎲 Laravel Enum

[![Author][ico-author]][link-author]
[![PHP Version][ico-php]][link-php]
[![Laravel Version][ico-laravel]][link-laravel]
[![Build Status][ico-actions]][link-actions]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![PHPStan Level][ico-phpstan]][link-phpstan]
[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![PER][ico-per]][link-per]
[![Total Downloads][ico-downloads]][link-downloads]

Laravel package to supercharge enum functionalities.

> [!TIP]
> Need a framework-agnostic solution? Consider using [🎲 Enum](https://github.com/cerbero90/enum) instead.


## 📦 Install

Via Composer:

``` bash
composer require cerbero/laravel-enum
```

## 🔮 Usage

* [🏷️ Meta](#%EF%B8%8F-meta)
* [🧺 Cases collection](#-cases-collection)
* [🪄 Magic translation](#-magic-translation)
* [💊 Encapsulation](#-encapsulation)
* [🏺 Artisan commands](#-artisan-commands)

This package provides all the functionalities of [🎲 Enum](https://github.com/cerbero90/enum) plus Laravel specific features.

To supercharge our enums, we just need to let them use the `Enumerates` trait:

```php
use Cerbero\LaravelEnum\Concerns\Enumerates;

enum Numbers: int
{
    use Enumerates;

    case One = 1;
    case Two = 2;
    case Three = 3;
}
```


### 🏷️ Meta

Laravel Enum enhances [Enum's meta](https://github.com/cerbero90/enum?tab=readme-ov-file#%EF%B8%8F-meta) by allowing us to attach meta with a class name. Such class is resolved by the Laravel container when calling the related meta method:

```php
use Cerbero\Enum\Attributes\Meta;

enum PayoutStatuses
{
    use Enumerates;

    #[Meta(handler: SentPayoutHandler::class)]
    case Sent;

    #[Meta(handler: OnHoldPayoutHandler::class)]
    case OnHold;

    #[Meta(handler: DeclinedPayoutHandler::class)]
    case Declined;
}
```

In the above enum, each case defines a `handler` meta with a class name. When a case calls its own `handler()` method, the related class is resolved out of the IoC container:

```php
// 🐢 instead of this
$handler = match ($payout->status) {
    PayoutStatuses::Sent => SentPayoutHandler::class,
    PayoutStatuses::OnHold => OnHoldPayoutHandler::class,
    PayoutStatuses::Declined => DeclinedPayoutHandler::class,
};

return Container::getInstance()->make($handler);


// 🐇 we can do this
return $payout->status->handler();
```

If we need to resolve a default class for most cases, we can attach the meta to the enum itself. The cases defining their own meta will override the default class:

```php
use Cerbero\Enum\Attributes\Meta;

#[Meta(handler: DefaultPayoutHandler::class)]
enum PayoutStatuses
{
    use Enumerates;

    #[Meta(handler: SentPayoutHandler::class)]
    case Sent;

    case OnHold;

    case Declined;
}
```

In the above example all cases calling the `handler()` method resolve the `DefaultPayoutHandler` class, except the `Sent` case that resolves `SentPayoutHandler`.

If the class to resolve is callable (i.e. it implements the `__invoke()` method), such class gets both resolved and executed:

```php
use Cerbero\Enum\Attributes\Meta;

enum PayoutStatuses
{
    use Enumerates;

    #[Meta(handle: SentPayoutHandler::class)]
    case Sent;

    #[Meta(handle: OnHoldPayoutHandler::class)]
    case OnHold;

    #[Meta(handle: DeclinedPayoutHandler::class)]
    case Declined;
}
```

In the above enum, each case defines a `handle` meta with a callable class. When a case calls its own `handle()` method, the related class gets resolved and its `__invoke()` method executed with any parameter we pass:

```php
// 🐢 instead of this
$handler = match ($payout->status) {
    PayoutStatuses::Sent => SentPayoutHandler::class,
    PayoutStatuses::OnHold => OnHoldPayoutHandler::class,
    PayoutStatuses::Declined => DeclinedPayoutHandler::class,
};

$handlePayout = Container::getInstance()->make($handler);

return $handlePayout($payout);


// 🐇 we can do this
return $payout->status->handle($payout);
```

If we need to run a default callable class for most cases, we can attach the meta to the enum itself. The cases defining their own meta will override the default callable class:

```php
use Cerbero\Enum\Attributes\Meta;

#[Meta(handle: DefaultPayoutHandler::class)]
enum PayoutStatuses
{
    use Enumerates;

    #[Meta(handle: SentPayoutHandler::class)]
    case Sent;

    case OnHold;

    case Declined;
}
```

In the above example all cases calling the `handle()` method execute the `DefaultPayoutHandler` class, except the `Sent` case that runs the `SentPayoutHandler`.

> [!TIP]
> Our IDE can autocomplete meta methods thanks to the [`enum:annotate` command](#-artisan-commands).
>
> Different class names of a meta are annotated by finding the interface or parent class they have in common.


### 🧺 Cases collection

The [original cases collection](https://github.com/cerbero90/enum?tab=readme-ov-file#-cases-collection) has been extended to integrate better with the Laravel framework.

The new cases collection implements the `Illuminate\Contracts\Support\Arrayable` and `Illuminate\Contracts\Support\Jsonable` contracts and it can be serialized into a JSON.

It also leverages the following Laravel traits:
- `Illuminate\Support\Traits\Conditionable` to run conditions while keeping the methods chain
- `Illuminate\Support\Traits\Macroable` to add methods to the collection at runtime
- `Illuminate\Support\Traits\Tappable` to run custom logic while keeping the methods chain

Furthermore the new collection allows us to `dump()` and `dd()` its cases:

```php
Numbers::collect()->dump();

Numbers::collect()->dd();
```

Cases collection can be cast in an Eloquent model to store multiple cases in one database column and to re-hydrate such cases back to a collection:

```php
use Cerbero\LaravelEnum\CasesCollection;

class User extends Model
{
    // before Laravel 11
    protected $casts = [
        'numbers' => CasesCollection::class . ':' . Numbers::class,
    ];

    // after Laravel 11
    protected function casts(): array
    {
        return [
            'numbers' => CasesCollection::of(Numbers::class),
        ];
    }
}
```

Now we can assign an array of names, values or cases to the `numbers` property and receive back a cases collection when we access such property:

```php
$user->numbers = ['One', 'Two'];

$user->numbers = [1, 2];

$user->numbers = [Numbers::One, Numbers::Two];

$user->numbers; // CasesCollection[Numbers::One, Numbers::Two]
```

The cases collection above is stored in the database as `["One","Two"]` if the enum is pure, or as `[1,2]` if the enum is backed.

The cast also supports bitwise backed enums, so for example if we have an enum of permissions implementing the `Bitwise` contract:

```php
use Cerbero\LaravelEnum\Contracts\Bitwise;

enum Permissions: int implements Bitwise
{
    use Enumerates;

    case CreatePost = 1;
    case UpdatePost = 2;
    case DeletePost = 4;
}
```

And we set the permissions cast on our Eloquent model:

```php
use Cerbero\LaravelEnum\CasesCollection;

class User extends Model
{
    // before Laravel 11
    protected $casts = [
        'permissions' => CasesCollection::class . ':' . Permissions::class,
    ];

    // after Laravel 11
    protected function casts(): array
    {
        return [
            'permissions' => CasesCollection::of(Permissions::class),
        ];
    }
}
```

We can assign a bitwise value or an array of values/bitwise backed cases to the `permissions` property and receive back a cases collection when we access such property:

```php
$user->permissions = 3;

$user->permissions = 1 | 2;

$user->permissions = Permissions::CreatePost->value | Permissions::UpdatePost->value;

$user->permissions = [1, 2];

$user->permissions = [Permissions::CreatePost, Permissions::UpdatePost];

$user->permissions; // CasesCollection[Permissions::CreatePost, Permissions::UpdatePost]
```

The cases collection above is stored in the database as `3`, the result of the `OR` bitwise operator.

### 🪄 Magic translation

On top of [Enum's magic](https://github.com/cerbero90/enum?tab=readme-ov-file#-magic), when a case calls an inaccessible method, and such case has no matching [meta](https://github.com/cerbero90/enum?tab=readme-ov-file#%EF%B8%8F-meta), Laravel Enum assumes that we want to access a translation:

```php
Numbers::One->description();

// lang/en/enums.php
return [
    Numbers::class => [
        'One' => [
            'description' => 'This is the case One.',
        ],
    ],
];
```

By default the translation key is resolved with `enums.{enum namespace}.{case name}.{inaccessible method}`. If needed, we can customize the translation key:

```php
use Cerbero\LaravelEnum\Enums;

Enums::translateFrom(function(UnitEnum $case, string $method) {
    return sprintf('custom.%s.%s.%s', $case::class, $case->name, $method);
});
```

The above logic will resolve the translation key with `custom.{enum namespace}.{case name}.{inaccessible method}`.

Also, we can pass named arguments to replace placeholders in our translations:

```php
return [
    Numbers::class => [
        'One' => [
            'description' => 'This is the case :value.',
        ],
    ],
];

// This is the case 1.
Numbers::One->description(value: 1);
```

> [!TIP]
> Our IDE can autocomplete translation methods thanks to the [`enum:annotate` command](#-artisan-commands).


### 💊 Encapsulation

Laravel Enum offers some extra traits to encapsulate Laravel features that deal with keys. We can hold our keys in an enum (each case is a key) and use Laravel features without ever having to repeat such keys.

The benefits of this approach are many:
- no flaky strings around our codebase
- no keys misspelling
- IDE autocompletion
- reviewing/managing all our application keys in a central location
- updating one key in one file instead of all its occurrences

To encapsulate the Laravel session, we can create an enum holding all our session keys and let it use `EnumeratesSessionKeys`. The enum can be either pure or backed:

```php
use Cerbero\LaravelEnum\Concerns\EnumeratesSessionKeys;

enum SessionKeys
{
    use EnumeratesSessionKeys;

    case CartItems;
    case OnboardingStep;
    case PageViews;
}
```

The `EnumeratesSessionKeys` trait also uses `Enumerates`, hence all the features of this package. We can now call all the Laravel session methods directly from our cases:

```php
SessionKeys::CartItems->exists();
SessionKeys::CartItems->missing();
SessionKeys::CartItems->hasValue();
SessionKeys::CartItems->get($default);
SessionKeys::CartItems->pull($default);
SessionKeys::CartItems->hasOldInput();
SessionKeys::CartItems->getOldInput($default);
SessionKeys::CartItems->put($value);
SessionKeys::CartItems->remember($callback);
SessionKeys::CartItems->push($value);
SessionKeys::CartItems->increment($amount);
SessionKeys::CartItems->decrement($amount);
SessionKeys::CartItems->flash($value);
SessionKeys::CartItems->now($value);
SessionKeys::CartItems->remove();
SessionKeys::CartItems->forget();
```

To encapsulate the Laravel cache, we can create a string backed enum holding all our cache keys and let it use `EnumeratesCacheKeys`:

```php
use Cerbero\LaravelEnum\Concerns\EnumeratesCacheKeys;

enum CacheKeys: string
{
    use EnumeratesCacheKeys;

    case PostComments = 'posts.{int $postId}.comments';
    case Tags = 'tags';
    case TeamMemberPosts = 'teams.{string $teamId}.users.{string $userId}.posts';
}
```

The `EnumeratesCacheKeys` trait also uses `Enumerates`, hence all the features of this package. We can now call all the Laravel cache methods after instantiating our cases:

```php
$teamMemberPosts = CacheKeys::TeamMemberPosts($teamId, $userId);

$teamMemberPosts->exists();
$teamMemberPosts->missing();
$teamMemberPosts->hasValue();
$teamMemberPosts->get($default);
$teamMemberPosts->pull($default);
$teamMemberPosts->put($value, $ttl);
$teamMemberPosts->add($value, $ttl);
$teamMemberPosts->increment($value);
$teamMemberPosts->decrement($value);
$teamMemberPosts->forever($value);
$teamMemberPosts->remember($ttl, $callback);
$teamMemberPosts->rememberForever($callback);
$teamMemberPosts->forget();
```

We can instantiate our cases statically and pass parameters to resolve dynamic keys. Such parameters replace the `{...}` placeholders in the cache keys:

```php
CacheKeys::PostComments($postId)->exists();

CacheKeys::Tags()->exists();

CacheKeys::TeamMemberPosts($teamId, $userId)->exists();
```

> [!TIP]
> Our IDE can autocomplete cache keys static methods thanks to the [`enum:annotate` command](#-artisan-commands).


### 🏺 Artisan commands

A handy set of Artisan commands is provided out of the box to interact with enums seamlessly.

We can annotate enums to ease our IDE autocompletion for case methods, meta methods, translations, etc. by running the `enum:annotate` command:

```bash
php artisan enum:annotate
```

If we don't provide any argument, a prompt appears to select all the enums that we want to annotate. By default enums are searched in the `app/Enums` directory. If we need to scan other folders, we can define them thanks to `Enums::paths()`:

```php
use Cerbero\LaravelEnum\Enums;

Enums::paths('app/Enums', 'domain/*/Enums');
```

In the above example, enums are searched in the `app/Enums` directory and in all `Enums` sub-folders belonging to `domain`, e.g. `domain/Posts/Enums`, `domain/Users/Enums`, etc.

If we want to annotate all the enums within the directories defined in `Enums::paths()`, we can simply add the option `--all`:

```bash
php artisan enum:annotate --all

php artisan enum:annotate -a
```

Alternatively we can provide one or more enums to the `enum:annotate` command. Both slashes and quoted backslashes are allowed to specify the enum namespaces:

```bash
php artisan enum:annotate App/Enums/Permissions 'App\Enums\PayoutStatuses'
```

Finally if we want to overwrite the method annotations already annotated on enums, we can add the option `--force`:

```bash
php artisan enum:annotate --force

php artisan enum:annotate -f
```


## 📆 Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🧪 Testing

``` bash
composer test
```

## 💞 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## 🧯 Security

If you discover any security related issues, please email andrea.marco.sartori@gmail.com instead of using the issue tracker.

## 🏅 Credits

- [Andrea Marco Sartori][link-author]
- [All Contributors][link-contributors]

## ⚖️ License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-author]: https://img.shields.io/static/v1?label=author&message=cerbero90&color=50ABF1&logo=twitter&style=flat-square
[ico-php]: https://img.shields.io/packagist/php-v/cerbero/laravel-enum?color=%234F5B93&logo=php&style=flat-square
[ico-laravel]: https://img.shields.io/static/v1?label=laravel&message=%E2%89%A59.0&color=ff2d20&logo=laravel&style=flat-square
[ico-version]: https://img.shields.io/packagist/v/cerbero/laravel-enum.svg?label=version&style=flat-square
[ico-actions]: https://img.shields.io/github/actions/workflow/status/cerbero90/laravel-enum/build.yml?branch=master&style=flat-square&logo=github
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-per]: https://img.shields.io/static/v1?label=compliance&message=PER&color=blue&style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cerbero90/laravel-enum.svg?style=flat-square&logo=scrutinizer
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cerbero90/laravel-enum.svg?style=flat-square&logo=scrutinizer
[ico-phpstan]: https://img.shields.io/badge/level-max-success?style=flat-square&logo=data:image/png;base64,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
[ico-downloads]: https://img.shields.io/packagist/dt/cerbero/laravel-enum.svg?style=flat-square

[link-author]: https://twitter.com/cerbero90
[link-php]: https://www.php.net
[link-laravel]: https://laravel.com
[link-packagist]: https://packagist.org/packages/cerbero/laravel-enum
[link-actions]: https://github.com/cerbero90/laravel-enum/actions?query=workflow%3Abuild
[link-per]: https://www.php-fig.org/per/coding-style/
[link-scrutinizer]: https://scrutinizer-ci.com/g/cerbero90/laravel-enum/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cerbero90/laravel-enum
[link-downloads]: https://packagist.org/packages/cerbero/laravel-enum
[link-phpstan]: https://phpstan.org/
[link-contributors]: ../../contributors
