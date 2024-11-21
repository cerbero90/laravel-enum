<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Providers;

use Cerbero\LaravelEnum\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;

/**
 * The package service provider.
 */
final class LaravelEnumServiceProvider extends ServiceProvider
{
    /**
     * The console commands to register.
     *
     * @var list<class-string<Command>>
     */
    private array $commands = [
        Commands\EnumAnnotateCommand::class,
    ];

    /**
     * Bootstrap the package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }

    /**
     * Register the package services.
     */
    public function register(): void
    {
        //
    }
}
