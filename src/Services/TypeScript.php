<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use Cerbero\Enum\Services\TypeScript as BaseTypeScript;

/**
 * The TypeScript service.
 */
final class TypeScript extends BaseTypeScript
{
    /**
     * Retrieve the path of the stub.
     */
    protected function stub(): string
    {
        return __DIR__ . '/../../stubs/typescript.stub';
    }
}
