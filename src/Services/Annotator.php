<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use Cerbero\LaravelEnum\Concerns\Enumerates;
use Cerbero\LaravelEnum\Data\MethodAnnotation;
use InvalidArgumentException;

/**
 * The enums annotator.
 */
final class Annotator
{
    /**
     * The regular expression to extract the use statements.
     */
    public const RE_USE_STATEMENTS = '~^use(?:[\s\S]+(?=^use))?.+~im';

    /**
     * The regular expression to extract the enum declaring line with potential attributes.
     */
    public const RE_ENUM = '~(^(?:#\[[\s\S]+)?^enum.+)~im';

    /**
     * The regular expression to extract the method annotations.
     */
    public const RE_METHOD_ANNOTATIONS = '~^ \* @method(?:[\s\S]+(?=@method))?.+~im';

    /**
     * Annotate the given enum.
     *
     * @template TEnum
     *
     * @param class-string<TEnum> $enum
     * @throws InvalidArgumentException
     */
    public function annotate(string $enum, bool $force = false): bool
    {
        $inspector = new Inspector($enum, $force);

        if (! $inspector->uses(Enumerates::class)) {
            throw new InvalidArgumentException("The enum {$enum} must use the trait " . Enumerates::class);
        }

        if (empty($annotations = $inspector->methodAnnotations())) {
            return true;
        }

        $docBlock = $inspector->docBlock();
        $filename = $inspector->filename();
        $oldContent = (string) file_get_contents($filename);
        $methodAnnotations = $this->formatMethodAnnotations($annotations);
        $useStatements = $this->formatUseStatements($inspector->useStatements());
        $newContent = (string) preg_replace(self::RE_USE_STATEMENTS, $useStatements, $oldContent, 1);

        $newContent = match (true) {
            empty($docBlock) => $this->addDocBlock($methodAnnotations, $newContent),
            str_contains($docBlock, '@method') => $this->replaceAnnotations($methodAnnotations, $newContent),
            default => $this->addAnnotations($methodAnnotations, $newContent, $docBlock),
        };

        return file_put_contents($filename, $newContent) !== false;
    }

    /**
     * Retrieve the formatted method annotations.
     *
     * @param array<string, MethodAnnotation> $annotations
     */
    private function formatMethodAnnotations(array $annotations): string
    {
        $mapped = array_map(fn(MethodAnnotation $annotation) => " * {$annotation}", $annotations);

        return implode(PHP_EOL, $mapped);
    }

    /**
     * Retrieve the formatted use statements.
     *
     * @param array<string, class-string> $statements
     */
    private function formatUseStatements(array $statements): string
    {
        array_walk($statements, function (string &$namespace, string $alias) {
            $namespace = "use {$namespace}" . (class_basename($namespace) == $alias ? ';' : " as {$alias};");
        });

        return implode(PHP_EOL, $statements);
    }

    /**
     * Add a docBlock with the given method annotations.
     */
    private function addDocBlock(string $methodAnnotations, string $content): string
    {
        $replacement = implode(PHP_EOL, ['/**', $methodAnnotations, ' */', '$1']);

        return (string) preg_replace(self::RE_ENUM, $replacement, $content, 1);
    }

    /**
     * Replace existing method annotations with the given method annotations.
     */
    private function replaceAnnotations(string $methodAnnotations, string $content): string
    {
        return (string) preg_replace(self::RE_METHOD_ANNOTATIONS, $methodAnnotations, $content, 1);
    }

    /**
     * Add the given method annotations to the provided docBlock.
     */
    private function addAnnotations(string $methodAnnotations, string $content, string $docBlock): string
    {
        $newDocBlock = str_replace(' */', implode(PHP_EOL, [' *', $methodAnnotations, ' */']), $docBlock);

        return str_replace($docBlock, $newDocBlock, $content);
    }
}
