<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use Cerbero\Enum\Services\Annotator as BaseAnnotator;

/**
 * The enums annotator.
 *
 * @template TEnum of \UnitEnum
 *
 * @extends BaseAnnotator<TEnum>
 */
final class Annotator extends BaseAnnotator
{
    /**
     * Instantiate the class.
     *
     * @param class-string<TEnum> $enum
     * @throws \InvalidArgumentException
     */
    public function __construct(protected string $enum)
    {
        $this->inspector = new Inspector($enum);
    }
}
