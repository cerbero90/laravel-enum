<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Actions;

use Cerbero\LaravelEnum\Enums;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Throwable;
use UnitEnum;

/**
 * The logic to handle an inaccessible case method call.
 */
class OnCall
{
    /**
     * Handle the call to an inaccessible case method.
     *
     * @param array<array-key, mixed> $arguments
     * @throws \ValueError
     */
    public function __invoke(object $case, string $name, array $arguments): mixed
    {
        try {
            /** @phpstan-ignore method.notFound */
            $value = $case->resolveMetaAttribute($name);
        } catch (Throwable $e) {
            /** @var UnitEnum $case */
            $key = Enums::resolveTranslationKey($case, $name);

            /** @var array{array<string, mixed>, ?string, bool} $arguments */
            return ($key === $translation = Lang::get($key, ...$arguments)) ? throw $e : $translation;
        }

        return is_string($value) && method_exists($value, '__invoke')
            ? call_user_func_array(App::make($value), $arguments) /** @phpstan-ignore argument.type */
            : $value;
    }
}
