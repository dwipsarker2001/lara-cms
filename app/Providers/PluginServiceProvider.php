<?php

namespace App\Providers;

use App\Support\PluginLoader;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register the PluginLoader as a singleton so it can be resolved
     * anywhere in the application via app(PluginLoader::class).
     */
    public function register(): void
    {
        $this->app->singleton(PluginLoader::class);
    }

    /**
     * Boot all plugins found in the /plugins directory.
     * This runs after all other service providers have booted.
     */
    public function boot(): void
    {
        $this->app->make(PluginLoader::class)->boot();
    }
}
