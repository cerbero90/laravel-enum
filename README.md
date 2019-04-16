# Laravel Enum

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Laravel package that introduces a new Artisan command to generate [Enum classes][link-enum].

It provides an easy syntax to specify and map constants in Enum classes while adding PHPDoc tags to make IDEs recognise what constants can be invoked as a method to instantiate an Enum class.


## Install

Via Composer

``` bash
$ composer require cerbero/laravel-enum
```


## Usage
@todo Add explanation.

``` bash
$ php artisan make:enum Progress "ON_HOLD|IN_PROGRESS|COMPLETE"
// default to lower case
```

``` bash
$ php artisan make:enum Progress "ON_HOLD=On hold|IN_PROGRESS=In progress|COMPLETE=Complete"
```

``` bash
$ php artisan make:enum PaymentGateway "PAYPAL=PayPal=1|STRIPE=Stripe=2"
```

``` bash
$ php artisan make:enum Status "OFF=0|ON=1" --path=app/Enums
```


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Testing

``` bash
$ composer test
```


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.


## Security

If you discover any security related issues, please email andrea.marco.sartori@gmail.com instead of using the issue tracker.


## Credits

- [Jodie Dunlop][link-jodie]
- [Enum PHP Library][link-enum]
- [Andrea Marco Sartori][link-author]


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/cerbero/laravel-enum.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/cerbero90/laravel-enum/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cerbero90/laravel-enum.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cerbero90/laravel-enum.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/cerbero/laravel-enum.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/cerbero/laravel-enum
[link-travis]: https://travis-ci.org/cerbero90/laravel-enum
[link-scrutinizer]: https://scrutinizer-ci.com/g/cerbero90/laravel-enum/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cerbero90/laravel-enum
[link-downloads]: https://packagist.org/packages/cerbero/laravel-enum
[link-author]: https://github.com/cerbero90
[link-jodie]: https://github.com/jodiedunlop
[link-enum]: https://github.com/rexlabsio/enum-php
