<?php

namespace Cerbero\LaravelEnum;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\LaravelEnum\Concerns\Enumerates;

/**
 * The backed enum to test.
 *
 * @method static string One()
 * @method static string Two()
 * @method static string Three()
 * @method string color()
 * @method string shape()
 */
#[Meta(color: 'green', shape: 'square')]
enum PureEnum
{
    use Enumerates;

    #[Meta(color: 'red', shape: 'triangle')]
    case One;

    case Two;

    #[Meta(color: 'blue', shape: 'circle')]
    case Three;
}
