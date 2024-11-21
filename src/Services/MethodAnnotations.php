<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use ArrayIterator;
use Cerbero\LaravelEnum\Data\MethodAnnotation;
use Cerbero\LaravelEnum\Enums;
use Illuminate\Support\Facades\Lang;
use IteratorAggregate;
use Traversable;

use function Cerbero\LaravelEnum\commonType;
use function Cerbero\LaravelEnum\namespaceExists;

/**
 * The method annotations collector.
 *
 * @implements IteratorAggregate<string, MethodAnnotation>
 */
final class MethodAnnotations implements IteratorAggregate
{
    /**
     * The regular expression to extract method annotations already annotated on the enum.
     */
    public const RE_METHOD = '~@method\s+((?:static)?\s*[^\s]+\s+([^\(]+).*)~';

    /**
     * Instantiate the class.
     *
     * @param Inspector<\UnitEnum> $inspector
     */
    public function __construct(
        private readonly Inspector $inspector,
        private readonly bool $force,
    ) {}

    /**
     * Retrieve the method annotations.
     *
     * @return ArrayIterator<string, MethodAnnotation>
     */
    public function getIterator(): Traversable
    {
        $annotations = [
            ...$this->forCaseNames(),
            ...$this->forMetaAttributes(),
            ...$this->forTranslations(),
            ...$this->force ? [] : $this->existing(),
        ];

        uasort($annotations, function (MethodAnnotation $a, MethodAnnotation $b) {
            return [$b->isStatic, $a->name] <=> [$a->isStatic, $b->name];
        });

        return new ArrayIterator($annotations);
    }

    /**
     * Retrieve the method annotations for the case names.
     *
     * @return array<string, MethodAnnotation>
     */
    public function forCaseNames(): array
    {
        $annotations = [];
        $method = $this->inspector->enumeratesCacheKeys() ? 'forCacheCase' : 'forCase';

        foreach ($this->inspector->cases() as $case) {
            $annotations[$case->name] = MethodAnnotation::$method($case);
        }

        /** @var array<string, MethodAnnotation> */
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

        foreach ($this->inspector->metaAttributeNames() as $name) {
            $returnTypes = array_map(function (mixed $case) use ($name) {
                $value = $case->resolveMetaAttribute($name);

                return is_string($value) && namespaceExists($value) ? $value : get_debug_type($value);
            }, $this->inspector->cases());

            $returnType = commonType(...$returnTypes);

            /** @var class-string $class */
            $annotations[$name] = method_exists($class = ltrim($returnType, '?'), '__invoke')
                ? MethodAnnotation::forInvokable($name, $class)
                : MethodAnnotation::instance($name, $returnType);
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

    /**
     * Retrieve the method annotations already annotated on the enum.
     *
     * @return array<string, MethodAnnotation>
     */
    public function existing(): array
    {
        $annotations = [];

        preg_match_all(self::RE_METHOD, $this->inspector->docBlock(), $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $annotations[$match[2]] = new MethodAnnotation($match[2], $match[1]);
        }

        return $annotations;
    }
}
