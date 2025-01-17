<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Providers;

use Cerbero\LaravelEnum\Commands;
use Cerbero\LaravelEnum\Enums;
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
        Commands\EnumMakeCommand::class,
        Commands\EnumTsCommand::class,
    ];

    /**
     * Bootstrap the package services.
     */
    public function boot(): void
    {
        Enums::setBasePath($this->app->basePath());

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }

        $this->publishes([
            __DIR__ . '/../../stubs' => $this->app->basePath('stubs/laravel-enum'),
        ], 'laravel-enum-stubs');
    }
}
