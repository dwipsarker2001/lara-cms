<?php

namespace App\Providers;

use App\Models\Collection;
use App\Models\Form;
use App\Models\Taxonomy;
use App\Support\PluginLoader;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        if (
            $this->app->environment('production')
            || config('app.env') === 'production'
            || str_starts_with((string) config('app.url'), 'https://')
            || request()->isSecure()
            || request()->header('X-Forwarded-Proto') === 'https'
            || request()->server('HTTP_X_FORWARDED_PROTO') === 'https'
            || request()->server('HTTPS') === 'on'
        ) {
            URL::forceScheme('https');
        }

        Blade::anonymousComponentNamespace('admin.components', 'admin');

        View::composer('admin.layout', function ($view) {
            $forms = Schema::hasTable('form_entries')
                ? Form::withCount(['entries' => function ($query) {
                    if (Schema::hasColumn('form_entries', 'status')) {
                        $query->where('status', 1);
                    }
                }])->orderBy('position')->get()
                : Form::orderBy('position')->get();

            $view->with('sidebarForms', $forms);

            $collections = Schema::hasTable('collections')
                ? Collection::orderBy('position')->get()
                : collect();

            $view->with('sidebarCollections', $collections);

            $taxonomies = Schema::hasTable('taxonomies')
                ? Taxonomy::withCount('terms')->orderBy('position')->orderBy('title')->get()
                : collect();

            $view->with('sidebarTaxonomies', $taxonomies);

            $pluginMenuItems = app(PluginLoader::class)->getAdminMenuItems();
            $view->with('sidebarPluginMenuItems', $pluginMenuItems);
        });
    }
}
