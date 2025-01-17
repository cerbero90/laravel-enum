<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use Cerbero\Enum\Data\GeneratingEnum;
use Cerbero\Enum\Enums\Backed;
use Cerbero\Enum\Services\Generator as BaseGenerator;

/**
 * The enums generator.
 */
final class Generator extends BaseGenerator
{
    /**
     * Instantiate the class.
     *
     * @param class-string<\UnitEnum> $namespace
     * @param string[] $cases
     */
    public function __construct(string $namespace, array $cases, private readonly Backed $backed)
    {
        $this->enum = new GeneratingEnum($namespace, $backed->back($cases));
    }

    /**
     * Retrieve the path of the stub.
     */
    protected function stub(): string
    {
        return $this->backed->is(Backed::bitwise)
            ? __DIR__ . '/../../stubs/bitwise.stub'
            : __DIR__ . '/../../stubs/enum.stub';
    }
}
