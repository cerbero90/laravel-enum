<?php

namespace Cerbero\LaravelEnum;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\LaravelEnum\Concerns\Enumerates;

/**
 * The backed enum to test.
 *
 * @method static int One()
 * @method static int Two()
 * @method static int Three()
 * @method string color()
 * @method string shape()
 * @method string description()
 * @method string dynamic(array<string, mixed> $replace = [])
 */
#[Meta(color: 'green', shape: 'square')]
enum BackedEnum: int
{
    use Enumerates;

    #[Meta(color: 'red', shape: 'triangle')]
    case One = 1;

    case Two = 2;

    #[Meta(color: 'blue', shape: 'circle')]
    case Three = 3;

    /**
     * Determine whether the case is odd.
     */
    public function isOdd(): bool
    {
        return $this->value % 2 != 0;
    }
}
