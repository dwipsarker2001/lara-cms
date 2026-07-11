<?php

namespace App\Providers;

use App\Blocks\BlockRegistry;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BlockRegistry::class);
    }

    public function boot(): void
    {
        Blade::anonymousComponentNamespace('admin.components', 'admin');
    }
}
