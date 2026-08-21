<?php

use App\Widgets\Widget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->tempPluginPath = base_path('plugins/test-widget-plugin');

    if (File::isDirectory($this->tempPluginPath)) {
        File::deleteDirectory($this->tempPluginPath);
    }

    File::makeDirectory($this->tempPluginPath.'/Widgets', 0755, true);
    File::makeDirectory($this->tempPluginPath.'/views/widgets', 0755, true);

    File::put($this->tempPluginPath.'/plugin.json', json_encode([
        'name' => 'Test Widget Plugin',
        'slug' => 'test-widget-plugin',
        'version' => '1.0.0',
    ]));

    File::put($this->tempPluginPath.'/Widgets/DummyStatsWidget.php', <<<'PHP'
<?php

namespace Plugins\TestWidgetPlugin\Widgets;

use App\Widgets\Widget;

class DummyStatsWidget extends Widget
{
    public static function zone(): string
    {
        return 'grid';
    }

    public function label(): string
    {
        return 'Dummy Stats';
    }

    public function render()
    {
        return '<div>dummy stats</div>';
    }
}
PHP);

    File::put($this->tempPluginPath.'/views/widgets/dummy-stats-widget.blade.php', '<div>dummy</div>');
});

afterEach(function () {
    if (File::isDirectory($this->tempPluginPath)) {
        File::deleteDirectory($this->tempPluginPath);
    }
});

it('discovers a widget class from a plugin Widgets directory', function () {
    /** @var WidgetRegistry $registry */
    $registry = app(WidgetRegistry::class);
    $registry->flush();

    $all = $registry->all();

    expect($all)->toHaveKey('dummy_stats_widget');
    expect($all['dummy_stats_widget'])->toBe('Plugins\TestWidgetPlugin\Widgets\DummyStatsWidget');
});

it('plugin widget reports the correct zone and label', function () {
    /** @var WidgetRegistry $registry */
    $registry = app(WidgetRegistry::class);
    $registry->flush();

    $class = $registry->get('dummy_stats_widget');
    expect($class)->not->toBeNull();

    $widget = $class::make([]);

    expect($widget->label())->toBe('Dummy Stats');
    expect($class::zone())->toBe('grid');
});

it('plugin widget renders successfully', function () {
    /** @var WidgetRegistry $registry */
    $registry = app(WidgetRegistry::class);
    $registry->flush();

    $class = $registry->get('dummy_stats_widget');
    $widget = $class::make([]);
    $html = $widget->render();

    expect((string) $html)->toContain('dummy stats');
});

it('does not overwrite core widgets with plugin widgets of the same type', function () {
    // Add a second plugin widget with a type that doesn't conflict with core
    File::put($this->tempPluginPath.'/Widgets/SafePluginWidget.php', <<<'PHP'
<?php

namespace Plugins\TestWidgetPlugin\Widgets;

use App\Widgets\Widget;

class SafePluginWidget extends Widget
{
    public static function zone(): string
    {
        return 'list';
    }

    public function label(): string
    {
        return 'Safe Plugin';
    }

    public function render()
    {
        return '<div>safe</div>';
    }
}
PHP);

    /** @var WidgetRegistry $registry */
    $registry = app(WidgetRegistry::class);
    $registry->flush();

    $all = $registry->all();

    // Both plugin widgets should be present
    expect($all)->toHaveKey('dummy_stats_widget');
    expect($all)->toHaveKey('safe_plugin_widget');

    // Core widgets should still be there
    expect($all)->toHaveKey('visitor');
    expect($all)->toHaveKey('updates_list');
});

it('make:plugin-widget command creates widget and view files', function () {
    $this->artisan('make:plugin-widget', [
        'plugin' => 'test-widget-plugin',
        'name' => 'RevenueWidget',
        '--zone' => 'grid',
    ])->assertSuccessful();

    expect(File::exists($this->tempPluginPath.'/Widgets/RevenueWidget.php'))->toBeTrue();
    expect(File::exists($this->tempPluginPath.'/views/widgets/revenue-widget.blade.php'))->toBeTrue();

    $contents = File::get($this->tempPluginPath.'/Widgets/RevenueWidget.php');
    expect($contents)->toContain('class RevenueWidget extends Widget');
    expect($contents)->toContain("return 'grid'");
    expect($contents)->toContain('test-widget-plugin::widgets.revenue-widget');
});

it('make:plugin-widget command fails for invalid zone', function () {
    $this->artisan('make:plugin-widget', [
        'plugin' => 'test-widget-plugin',
        'name' => 'BadWidget',
        '--zone' => 'invalid',
    ])->assertFailed();
});

it('make:plugin-widget command fails if plugin does not exist', function () {
    $this->artisan('make:plugin-widget', [
        'plugin' => 'nonexistent-plugin',
        'name' => 'SomeWidget',
    ])->assertFailed();
});
