<?php

namespace App\Providers;

use App\Blocks\BlockRegistry;
use App\Models\Form;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
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

        View::composer('admin.layout', function ($view) {
            $view->with('sidebarForms', Form::orderBy('position')->get());
        });
    }
}
