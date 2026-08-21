<?php

namespace Plugins\DemoWidgets\Widgets;

use App\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class SystemHealthWidget extends Widget
{
    public static function zone(): string
    {
        return 'list';
    }

    public function label(): string
    {
        return 'System Health';
    }

    public function render()
    {
        $checks = collect([
            [
                'label' => 'Database Connection',
                'status' => $this->checkDatabase(),
                'icon' => 'database',
            ],
            [
                'label' => 'Cache Driver',
                'status' => config('cache.default') !== 'file' ? 'ok' : 'warn',
                'icon' => 'cache',
                'detail' => ucfirst(config('cache.default')),
            ],
            [
                'label' => 'Storage Writable',
                'status' => is_writable(storage_path()) ? 'ok' : 'error',
                'icon' => 'storage',
            ],
            [
                'label' => 'App Environment',
                'status' => app()->isProduction() ? 'ok' : 'warn',
                'icon' => 'env',
                'detail' => app()->environment(),
            ],
            [
                'label' => 'Debug Mode',
                'status' => config('app.debug') ? 'error' : 'ok',
                'icon' => 'debug',
                'detail' => config('app.debug') ? 'ON' : 'OFF',
            ],
            [
                'label' => 'PHP Version',
                'status' => version_compare(PHP_VERSION, '8.2', '>=') ? 'ok' : 'warn',
                'icon' => 'php',
                'detail' => PHP_VERSION,
            ],
        ]);

        $allOk = $checks->every(fn ($c) => $c['status'] === 'ok');
        $hasErrors = $checks->contains(fn ($c) => $c['status'] === 'error');

        return view('demo-widgets::widgets.system-health', compact('checks', 'allOk', 'hasErrors'));
    }

    protected function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();

            return 'ok';
        } catch (\Throwable) {
            return 'error';
        }
    }
}
