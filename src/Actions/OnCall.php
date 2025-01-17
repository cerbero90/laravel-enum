<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Actions;

use Cerbero\LaravelEnum\Enums;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use InvalidArgumentException;
use Throwable;
use UnitEnum;

use function Cerbero\LaravelEnum\namespaceExists;

/**
 * The logic to handle an inaccessible case method call.
 */
class OnCall
{
    /**
     * Handle the call to an inaccessible case method.
     *
     * @param array<array-key, mixed> $arguments
     * @throws InvalidArgumentException
     * @throws \ValueError
     */
    public function __invoke(UnitEnum $case, string $name, array $arguments): mixed
    {
        try {
            $value = $case->resolveMetaAttribute($name);
        } catch (Throwable $e) {
            return $this->translate($case, $name, $arguments) ?? throw $e;
        }

        return match (true) {
            ! is_string($value) => $value, /** @phpstan-ignore-next-line argument.type */
            method_exists($value, '__invoke') => call_user_func_array(App::make($value), $arguments),
            namespaceExists($value) => App::make($value, $arguments),
            default => $value,
        };
    }

    /**
     * Retrieve the translation of the given key.
     *
     * @param array<array-key, mixed> $arguments
     * @throws InvalidArgumentException
     */
    protected function translate(UnitEnum $case, string $name, array $arguments): ?string
    {
        $key = Enums::resolveTranslationKey($case, $name);

        if (Lang::get($key) === $key) {
            return null;
        }

        if ($arguments && array_is_list($arguments)) {
            $method = sprintf('%s::%s->%s()', $case::class, $case->name, $name);

            throw new InvalidArgumentException("The method {$method} must be called with its named arguments");
        }

        /** @var string */
        return Lang::get($key, $arguments);
    }
}
