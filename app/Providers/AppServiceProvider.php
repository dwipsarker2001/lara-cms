<?php

namespace App\Providers;

use App\Models\Collection;
use App\Models\Form;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WidgetRegistry::class);
    }

    public function boot(): void
    {
        Blade::anonymousComponentNamespace('admin.components', 'admin');

        View::composer('admin.layout', function ($view) {
            $forms = Schema::hasTable('form_entries')
                ? Form::withCount('entries')->orderBy('position')->get()
                : Form::orderBy('position')->get();

            $view->with('sidebarForms', $forms);

            $collections = Schema::hasTable('collections')
                ? Collection::orderBy('position')->get()
                : collect();

            $view->with('sidebarCollections', $collections);
        });
    }
}
