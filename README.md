# 🎲 Laravel Enum

[![Author][ico-author]][link-author]
[![PHP Version][ico-php]][link-php]
[![Laravel Version][ico-laravel]][link-laravel]
[![Build Status][ico-actions]][link-actions]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![PHPStan Level][ico-phpstan]][link-phpstan]
[![Total Downloads][ico-downloads]][link-downloads]
[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![PER][ico-per]][link-per]

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
  * [🗄️ Cache](#-cache)
  * [🔓 Session](#-session)
* [🦾 Artisan commands](#-artisan-commands)
  * [🗒️ enum:annotate](#%EF%B8%8F-enumannotate)
  * [🏗️ enum:make](#%EF%B8%8F-enummake)
  * [💙 enum:ts](#-enumts)

This package provides all the functionalities of [🎲 Enum](https://github.com/cerbero90/enum) plus Laravel-specific features.

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

Laravel Enum extends [Enum's meta](https://github.com/cerbero90/enum?tab=readme-ov-file#%EF%B8%8F-meta) by allowing us to attach meta with a class name. This class is resolved by the Laravel container when invoking the corresponding meta method:

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

In the enum above, each case specifies a `handler` meta with a class name. When a case calls its `handler()` meta method, the corresponding class is resolved through the IoC container:

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

If we need to resolve a default class for most cases, we can attach the meta to the enum itself. Cases with their own meta override the default class:

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

In the example above, all cases calling the `handler()` method resolve the `DefaultPayoutHandler` class, except for the `Sent` case that resolves `SentPayoutHandler`.

If the class to be resolved is callable (i.e., it implements the `__invoke()` method), that class will be both resolved and executed:

```php
use Cerbero\Enum\Attributes\Meta;

enum PayoutStatuses
{
    use Enumerates;

    #[Meta(handlePayout: SentPayoutHandler::class)]
    case Sent;

    #[Meta(handlePayout: OnHoldPayoutHandler::class)]
    case OnHold;

    #[Meta(handlePayout: DeclinedPayoutHandler::class)]
    case Declined;
}
```

In the enum above, each case specifies a `handlePayout` meta with a callable class. When a case calls its `handlePayout()` method, the corresponding class is resolved and its `__invoke()` method is executed with any parameters passed:

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
return $payout->status->handlePayout($payout);
```

If we need to run a default callable class for most cases, we can attach the meta to the enum itself. Cases with their own meta override the default callable class:

```php
use Cerbero\Enum\Attributes\Meta;

#[Meta(handlePayout: DefaultPayoutHandler::class)]
enum PayoutStatuses
{
    use Enumerates;

    #[Meta(handlePayout: SentPayoutHandler::class)]
    case Sent;

    case OnHold;

    case Declined;
}
```

In the example above, all cases calling the `handlePayout()` method execute the `DefaultPayoutHandler` class, except for the `Sent` case, which runs the `SentPayoutHandler`.

> [!TIP]
> Our IDE can autocomplete meta methods thanks to the [`enum:annotate` command](#%EF%B8%8F-enumannotate).
>
> Class names in meta are annotated by identifying the common interface or parent class they share.


### 🧺 Cases collection

The [original cases collection](https://github.com/cerbero90/enum?tab=readme-ov-file#-cases-collection) has been extended for better integration with the Laravel framework.

The new cases collection implements the `Illuminate\Contracts\Support\Arrayable` and `Illuminate\Contracts\Support\Jsonable` contracts and it can be serialized into a JSON.

It also leverages the following Laravel traits:
- `Illuminate\Support\Traits\Conditionable` for conditional chaining of methods
- `Illuminate\Support\Traits\Macroable` for adding methods to the collection at runtime
- `Illuminate\Support\Traits\Tappable` for running custom logic while keeping method chaining

Additionally, the new collection enables us to `dump()` and `dd()` its cases:

```php
Numbers::collect()->dump();

Numbers::collect()->dd();
```

Cases collection can be cast in an Eloquent model to store multiple cases in a single database column and then re-hydrate those cases back into a collection:

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

Once the cast is set, we can assign an array of names, values or cases to the `numbers` property of the model and receive a cases collection when accessing the property:

```php
$user->numbers = ['One', 'Two'];

$user->numbers = [1, 2];

$user->numbers = [Numbers::One, Numbers::Two];

$user->numbers; // CasesCollection[Numbers::One, Numbers::Two]
```

The cases collection above is stored in the database as `["One","Two"]` for a pure enum, or as `[1,2]` for a backed enum.

The cast also supports bitwise backed enums, so for instance, if we have a `Permissions` enum implementing the `Bitwise` contract:

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

and we cast the `permissions` property in our Eloquent model:

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

we can assign a bitwise value or an array of bitwise values/cases to the `permissions` property and receive a cases collection in return:

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

On top of [Enum's magic](https://github.com/cerbero90/enum?tab=readme-ov-file#-magic), when a case calls an inaccessible method without a corresponding [meta](#%EF%B8%8F-meta) match, Laravel Enum assumes that we want to access a translation:

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

By default the translation key is resolved as `enums.{enum namespace}.{case name}.{inaccessible method}`. If customization is needed, we can adjust the translation key:

```php
use Cerbero\LaravelEnum\Enums;

Enums::translateFrom(function(UnitEnum $case, string $method) {
    return sprintf('custom.%s.%s.%s', $case::class, $case->name, $method);
});
```

The above logic will resolve the translation key as `custom.{enum namespace}.{case name}.{inaccessible method}`.

Additionally, we can pass named arguments to replace placeholders within our translations:

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
> Our IDE can autocomplete translation methods thanks to the [`enum:annotate` command](#%EF%B8%8F-enumannotate).


### 💊 Encapsulation

Laravel Enum offers extra traits to encapsulate Laravel features that deal with keys. By defining keys as enum cases, we can leverage Laravel features without having to remember or repeat such keys.

The benefits of this approach are many:
- avoiding scattered, error-prone strings throughout the codebase
- preventing key misspellings
- enabling IDE autocompletion
- reviewing all application keys in a central location
- updating keys in one place rather than replacing all instances


#### 🗄️ Cache

To encapsulate the Laravel cache, we can define a backed enum with all our cache keys and apply the `EnumeratesCacheKeys` trait:

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

The `EnumeratesCacheKeys` trait incorporates `Enumerates`, hence all the features of this package. We can now leverage all the Laravel cache methods by statically calling enum cases:

```php
CacheKeys::Tags()->exists();
CacheKeys::Tags()->missing();
CacheKeys::Tags()->hasValue();
CacheKeys::Tags()->get($default);
CacheKeys::Tags()->pull($default);
CacheKeys::Tags()->put($value, $ttl);
CacheKeys::Tags()->set($value, $ttl);
CacheKeys::Tags()->add($value, $ttl);
CacheKeys::Tags()->increment($value);
CacheKeys::Tags()->decrement($value);
CacheKeys::Tags()->forever($value);
CacheKeys::Tags()->remember($ttl, $callback);
CacheKeys::Tags()->rememberForever($callback);
CacheKeys::Tags()->sear($callback);
CacheKeys::Tags()->forget();
CacheKeys::Tags()->delete();
CacheKeys::Tags()->lock($seconds, $owner);
CacheKeys::Tags()->restoreLock($owner);
```

When calling cases statically, we can pass parameters to resolve dynamic keys. Such parameters replace the `{...}` placeholders in the cache keys:

```php
CacheKeys::TeamMemberPosts($teamId, $userId)->exists();
```


#### 🔓 Session

To encapsulate the Laravel session, we can define a backed enum with all our session keys and apply the `EnumeratesSessionKeys` trait:

```php
use Cerbero\LaravelEnum\Concerns\EnumeratesSessionKeys;

enum SessionKeys
{
    use EnumeratesSessionKeys;

    case CartItems = 'cart-items';
    case OnboardingStep = 'onboarding-step';
    case FormsData = 'forms.{int $formId}.data';
}
```

The `EnumeratesSessionKeys` trait incorporates `Enumerates`, hence all the features of this package. We can now leverage all the Laravel session methods by statically calling enum cases:

```php
SessionKeys::CartItems()->exists();
SessionKeys::CartItems()->missing();
SessionKeys::CartItems()->hasValue();
SessionKeys::CartItems()->get($default);
SessionKeys::CartItems()->pull($default);
SessionKeys::CartItems()->hasOldInput();
SessionKeys::CartItems()->getOldInput($default);
SessionKeys::CartItems()->put($value);
SessionKeys::CartItems()->remember($callback);
SessionKeys::CartItems()->push($value);
SessionKeys::CartItems()->increment($amount);
SessionKeys::CartItems()->decrement($amount);
SessionKeys::CartItems()->flash($value);
SessionKeys::CartItems()->now($value);
SessionKeys::CartItems()->remove();
SessionKeys::CartItems()->forget();
```

When calling cases statically, we can pass parameters to resolve dynamic keys. Such parameters replace the `{...}` placeholders in the session keys:

```php
SessionKeys::FormsData($formId)->exists();
```

> [!TIP]
> Our IDE can autocomplete case static methods thanks to the [`enum:annotate` command](#%EF%B8%8F-enumannotate).


### 🦾 Artisan commands

A handy set of Artisan commands is provided out of the box to interact with enums seamlessly.

Some commands generate enums or related files. If we want to customize such files, we can publish their stubs:

```bash
php artisan vendor:publish --tag=laravel-enum-stubs
```

After publishing, the stubs can be modified within the `stubs/laravel-enum` directory, located at the root of our application.

Certain commands supports the `--all` option to reference all enums in our application. By default, enums are searched in the `app/Enums` directory, but we can scan other folders as well:

```php
use Cerbero\LaravelEnum\Enums;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Enums::setPaths('app/Enums', 'domain/*/Enums');
    }
}
```

In the example above, enums are searched in the `app/Enums` directory and all `Enums` sub-directories within `domain`, such as `domain/Posts/Enums`, `domain/Users/Enums`, etc.

#### 🗒️ enum:annotate

IDEs can autocomplete case static methods, meta methods, translations, etc. by running the `enum:annotate` command:

```bash
php artisan enum:annotate
```

If we don't provide any argument, a prompt appears to choose which enums to annotate. Or, we can simply use the `--all` option to annotate all enums:

```bash
php artisan enum:annotate --all

php artisan enum:annotate -a
```

Alternatively, we can provide one or more enums directly to the `enum:annotate` command. Both slashes and quoted backslashes are acceptable for defining enum namespaces:

```bash
php artisan enum:annotate App/Enums/Permissions "App\Enums\PayoutStatuses"
```

Lastly, if we wish to overwrite existing method annotations on enums, we can include the `--force` option:

```bash
php artisan enum:annotate --force

php artisan enum:annotate -f
```

#### 🏗️ enum:make

The `enum:make` command allows us to create a new, automatically annotated enum with the cases we need:

```bash
php artisan enum:make
```

If no arguments are given, prompts will guide us through defining the enum namespace, backing type and cases. Otherwise, all these details can be typed via command line:

```bash
php artisan enum:make App/Enums/Enum CaseOne CaseTwo

php artisan enum:make "App\Enums\Enum" CaseOne CaseTwo
```

For creating backed enums, we can manually set custom values for each case:

```bash
php artisan enum:make App/Enums/Enum CaseOne=value1 CaseTwo=value2
```

Or, we can automatically assign values to cases by using the `--backed` option:

```bash
php artisan enum:make App/Enums/Enum CaseOne CaseTwo --backed=int0
```

The `--backed` option accepts these values:

- `int0`: assigns incremental integers starting from 0 (0, 1, 2...)
- `int1`: assigns incremental integers starting from 1 (1, 2, 3...)
- `bitwise`: assigns incremental bitwise values (1, 2, 4...)
- `snake`: assigns the case name in snake case (case_one, case_two...)
- `kebab`: assigns the case name in kebab case (case-one, case-two...)
- `camel`: assigns the case name in camel case (caseOne, caseTwo...)
- `lower`: assigns the case name in lower case (caseone, casetwo...)
- `upper`: assigns the case name in upper case (CASEONE, CASETWO...)

To overwrite an existing enum, we can include the `--force` option:

```bash
php artisan enum:make App/Enums/Enum CaseOne CaseTwo --force

php artisan enum:make App/Enums/Enum CaseOne CaseTwo -f
```

We can generate the TypeScript counterpart of the newly created enum by adding the `--typescript` option:

```bash
php artisan enum:make App/Enums/Enum CaseOne CaseTwo --typescript

php artisan enum:make App/Enums/Enum CaseOne CaseTwo -t
```

#### 💙 enum:ts

The `ts` command converts enums to their TypeScript equivalents, ensuring backend and frontend synchronization:

```bash
php artisan enum:ts
```

If we don't provide any argument, a prompt appears to choose which enums to synchronize. Or, we can simply use the `--all` option to synchronize all enums:

```bash
php artisan enum:ts --all

php artisan enum:ts -a
```

Alternatively, we can provide one or more enums directly to the `enum:ts` command. Both slashes and quoted backslashes are acceptable for defining enum namespaces:

```bash
php artisan enum:ts App/Enums/Permissions "App\Enums\PayoutStatuses"
```

By default, enums are synchronized to `resources/js/enums/index.ts`, but this can be easily customized:

```php
use Cerbero\LaravelEnum\Enums;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // custom static path
        Enums::setTypeScript('frontend/enums/index.ts');

        // custom dynamic path
        Enums::setTypeScript(function (string $enum) {
            $domain = explode('\\', $enum)[1];

            return "resources/js/modules/{$domain}/enums.ts";
        });
    }
}
```

As shown above, we can either define a static path for TypeScript enums or dynamically set the TypeScript path for an enum based on its namespace.

To update enums that have already been synchronized, we can use the `--force` option:

```bash
php artisan enum:ts App/Enums/Enum --force

php artisan enum:ts App/Enums/Enum -f
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

[ico-author]: https://img.shields.io/badge/author-cerbero90-blue?logo=x&style=flat-square&logoSize=auto
[ico-php]: https://img.shields.io/packagist/php-v/cerbero/laravel-enum?color=%23767bb5&logo=php&style=flat-square&logoSize=auto
[ico-laravel]: https://img.shields.io/static/v1?label=laravel&message=%E2%89%A59.0&color=ff2d20&logo=laravel&style=flat-square&logoSize=auto
[ico-version]: https://img.shields.io/packagist/v/cerbero/laravel-enum.svg?label=version&style=flat-square
[ico-actions]: https://img.shields.io/github/actions/workflow/status/cerbero90/laravel-enum/build.yml?branch=master&style=flat-square&logo=github&logoSize=auto
[ico-license]: https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square
[ico-per]: https://img.shields.io/static/v1?label=compliance&message=PER&color=blue&style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cerbero90/laravel-enum.svg?style=flat-square&logo=scrutinizer&logoSize=auto
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cerbero90/laravel-enum.svg?style=flat-square&logo=scrutinizer&logoSize=auto
[ico-phpstan]: https://img.shields.io/badge/level-max-success?style=flat-square&logo=data:image/png;base64,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&logoSize=auto
[ico-downloads]: https://img.shields.io/packagist/dt/cerbero/laravel-enum.svg?style=flat-square

[link-author]: https://x.com/cerbero90
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
