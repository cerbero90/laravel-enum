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
 * @method int handle(int $a, int $b)
 * @method string description()
 * @method string dynamic(mixed $value)
 */
#[Meta(color: 'green', shape: 'square', handle: DefaultHandler::class)]
enum BackedEnum: int
{
    use Enumerates;

    #[Meta(color: 'red', shape: 'triangle')]
    case One = 1;

    #[Meta(handle: TwoHandler::class)]
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
