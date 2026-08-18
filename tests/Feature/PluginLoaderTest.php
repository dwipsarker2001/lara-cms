<?php

use App\Blocks\BlockRegistry;
use App\Support\PluginLoader;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->tempPluginPath = base_path('plugins/test-dummy-plugin');

    if (File::isDirectory($this->tempPluginPath)) {
        File::deleteDirectory($this->tempPluginPath);
    }

    File::makeDirectory($this->tempPluginPath.'/Blocks', 0755, true);
    File::makeDirectory($this->tempPluginPath.'/src/Support', 0755, true);
    File::makeDirectory($this->tempPluginPath.'/views', 0755, true);

    File::put($this->tempPluginPath.'/plugin.json', json_encode([
        'name' => 'Test Dummy Plugin',
        'slug' => 'test-dummy-plugin',
        'version' => '1.0.0',
    ]));

    File::put($this->tempPluginPath.'/routes.php', '<?php Illuminate\Support\Facades\Route::get("/test-dummy-plugin-route", fn() => "ok");');

    File::put($this->tempPluginPath.'/Blocks/DummyPluginBlock.php', '<?php
namespace Plugins\TestDummyPlugin\Blocks;

use App\Blocks\Block;
use App\Blocks\Field;

class DummyPluginBlock extends Block
{
    public string $name = "dummy_plugin_block";
    public string $label = "Dummy Plugin Block";

    public function fields(): array
    {
        return [
            Field::make("title")->text()->label("Title"),
        ];
    }
}
');
});

afterEach(function () {
    if (File::isDirectory($this->tempPluginPath)) {
        File::deleteDirectory($this->tempPluginPath);
    }
});

it('boots plugins and registers routes, views, and block discovery', function () {
    /** @var PluginLoader $loader */
    $loader = app(PluginLoader::class);
    $loader->boot();

    expect($loader->has('test-dummy-plugin'))->toBeTrue();

    // Verify route was registered
    $response = $this->get('/test-dummy-plugin-route');
    $response->assertOk();
    $response->assertSee('ok');

    // Verify block auto-discovery
    /** @var BlockRegistry $registry */
    $registry = app(BlockRegistry::class);
    $registry->flush();
    $blocks = $registry->all();

    expect($blocks)->toHaveKey('dummy_plugin_block');
    expect($blocks['dummy_plugin_block']->label)->toBe('Dummy Plugin Block');
});

it('collects admin menu items defined in plugins', function () {
    File::put($this->tempPluginPath.'/plugin.json', json_encode([
        'name' => 'Test Dummy Plugin',
        'slug' => 'test-dummy-plugin',
        'version' => '1.0.0',
        'admin_menu' => [
            [
                'label' => 'Client Portal',
                'url' => '/admin/client-portal',
                'icon' => 'fa-solid fa-users',
                'badge' => 'New',
                'order' => 1,
            ],
        ],
    ]));

    /** @var PluginLoader $loader */
    $loader = new PluginLoader;
    $loader->boot();

    $items = $loader->getAdminMenuItems();
    expect($items)->toBeArray();
    expect(count($items))->toBeGreaterThanOrEqual(1);
    expect($items[0]['label'])->toBe('Client Portal');
    expect($items[0]['badge'])->toBe('New');
});

it('registers plugin migrations and co-located block views', function () {
    $migrationsDir = $this->tempPluginPath.'/database/migrations';
    File::makeDirectory($migrationsDir, 0755, true);

    $blockDir = $this->tempPluginPath.'/Blocks/CustomWidget';
    File::makeDirectory($blockDir, 0755, true);

    File::put($blockDir.'/CustomWidget.php', '<?php
namespace Plugins\TestDummyPlugin\Blocks\CustomWidget;

use App\Blocks\Block;
use App\Blocks\Field;

class CustomWidget extends Block
{
    public string $name = "custom_widget";
    public string $label = "Custom Widget";

    public function fields(): array
    {
        return [
            Field::make("headline")->text()->label("Headline"),
        ];
    }
}
');
    File::put($blockDir.'/view.blade.php', '<div>Widget: {{ $headline ?? "Co-Located Block!" }}</div>');

    $loader = new PluginLoader;
    $loader->boot();

    $migratorPaths = app('migrator')->paths();
    expect($migratorPaths)->toContain($migrationsDir);

    $registry = app(BlockRegistry::class);
    $registry->flush();
    $blocks = $registry->all();

    expect($blocks)->toHaveKey('custom_widget');
    $block = $blocks['custom_widget'];
    $viewName = $block->view();
    expect(view()->exists($viewName))->toBeTrue();
});
