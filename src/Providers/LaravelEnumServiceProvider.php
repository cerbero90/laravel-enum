<?php

namespace Cerbero\LaravelEnum\Providers;

use Illuminate\Support\ServiceProvider;
use Cerbero\LaravelEnum\Console\Commands\EnumMakeCommand;

/**
 * The Laravel Enum service provider.
 *
 */
class LaravelEnumServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                EnumMakeCommand::class,
            ]);
        }
    }
}
