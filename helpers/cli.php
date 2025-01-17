<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Closure;
use Illuminate\Console\OutputStyle;
use Throwable;

/**
 * Output the outcome of the given enum operation.
 *
 * @param class-string<\UnitEnum> $namespace
 * @param Closure(): bool $callback
 */
function output(OutputStyle $output, string $enum, Closure $callback): bool
{
    $error = null;

    try {
        $succeeded = $callback();
    } catch (Throwable $e) {
        $succeeded = false;
        $error = "\e[38;2;220;38;38m{$e?->getMessage()}\e[0m";
    }

    if ($succeeded) {
        $output->writeln("\e[48;2;163;230;53m\e[38;2;63;98;18m\e[1m DONE \e[0m {$enum}" . PHP_EOL);
    } else {
        $output->writeln("\e[48;2;248;113;113m\e[38;2;153;27;27m\e[1m FAIL \e[0m {$enum} {$error}" . PHP_EOL);
    }

    return $succeeded;
}

/**
 * Annotate the given enum in a new process.
 *
 * @param class-string<\UnitEnum> $enum
 */
function runAnnotate(string $enum, bool $force = false): bool
{
    return runEnum("annotate \"{$enum}\"" . ($force ? ' --force' : ''));
}

/**
 * Run the enum CLI in a new process.
 */
function runEnum(string $command): bool
{
    // Once an enum is loaded, PHP accesses it from the memory and not from the disk.
    // Since our commands write on disk, the enum in memory might get out of sync.
    // To make sure that we are dealing with the current contents of such enum,
    // we spin a new process to load the latest state of the enum in memory.
    $cmd = vsprintf('"%s" "%s" %s 2>&1', [
        PHP_BINARY,
        Enums::basePath('artisan'),
        "enum:{$command}",
    ]);

    ob_start();

    $succeeded = passthru($cmd, $status) === null;

    ob_end_clean();

    return $succeeded;
}

/**
 * Synchronize the given enum in TypeScript within a new process.
 *
 * @param class-string<\UnitEnum> $enum
 */
function runTs(string $enum, bool $force = false): bool
{
    return runEnum("ts \"{$enum}\"" . ($force ? ' --force' : ''));
}
