<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use Cerbero\Enum\Services\MethodAnnotations as BaseMethodAnnotations;
use Cerbero\LaravelEnum\Data\MethodAnnotation;
use Cerbero\LaravelEnum\Enums;
use Illuminate\Support\Facades\Lang;

use function Cerbero\LaravelEnum\metaReturnType;

/**
 * The method annotations collector.
 *
 * @property-read Inspector<\UnitEnum> $inspector
 */
final class MethodAnnotations extends BaseMethodAnnotations
{
    /**
     * Retrieve all the method annotations.
     *
     * @return array<string, MethodAnnotation>
     */
    public function all(): array
    {
        /** @var array<string, MethodAnnotation> */
        return [
            ...$this->forCaseNames(),
            ...$this->forMetaAttributes(),
            ...$this->forTranslations(),
            ...$this->includeExisting ? $this->existing() : [],
        ];
    }

    /**
     * Retrieve the method annotations for the case names.
     *
     * @return array<string, MethodAnnotation>
     */
    public function forCaseNames(): array
    {
        $annotations = [];

        foreach ($this->inspector->cases() as $case) {
            $annotations[$case->name] = $this->inspector->caseAnnotation($case);
        }

        return $annotations;
    }

    /**
     * Retrieve the method annotations for the meta attributes.
     *
     * @return array<string, MethodAnnotation>
     */
    public function forMetaAttributes(): array
    {
        $annotations = [];
        $cases = $this->inspector->cases();

        foreach ($this->inspector->metaAttributeNames() as $meta) {
            $returnType = metaReturnType($meta, $cases);

            /** @var class-string $class */
            $annotations[$meta] = method_exists($class = ltrim($returnType, '?'), '__invoke')
                ? MethodAnnotation::forInvokable($meta, $class)
                : MethodAnnotation::instance($meta, $returnType);
        }

        return $annotations;
    }

    /**
     * Retrieve the method annotations for the translations.
     *
     * @return array<string, MethodAnnotation>
     */
    public function forTranslations(): array
    {
        $annotations = [];

        foreach ($this->inspector->cases() as $case) {
            $key = Enums::resolveTranslationKey($case);

            if ($key === $translations = Lang::get($key)) {
                continue;
            }

            /** @var array<string, string> $translations */
            foreach ($translations as $name => $translation) {
                $annotations[$name] ??= MethodAnnotation::forTranslation($name, $translation);
            }
        }

        return $annotations;
    }
}
