<?php

use App\Models\Admin;
use App\Models\PageView;
use App\Widgets\WebsiteAnalyticsWidget;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    // Clear page views table before each test
    PageView::truncate();
});

it('does not track page views for admin routes or non-GET requests', function () {
    $admin = Admin::factory()->create();
    actingAs($admin, 'admin');

    // Visit admin dashboard (should NOT track)
    get(route('admin.dashboard'))->assertStatus(200);
    expect(PageView::count())->toBe(0);

    // Make a POST request to a public route (if any, e.g. login attempt) - should NOT track
    $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
    expect(PageView::count())->toBe(0);
});

it('tracks page views only for public GET requests', function () {
    // Visit login page (should track)
    get('/login')->assertStatus(200);

    expect(PageView::count())->toBe(1);

    $pageView = PageView::first();
    expect($pageView->url)->toBe('login');
    expect($pageView->ip)->toBe('127.0.0.1');
});

it('returns correct dynamic metrics and series in WebsiteAnalyticsWidget', function () {
    // Seed some page views
    // Today
    PageView::forceCreate([
        'url' => '/login',
        'ip' => '192.168.1.1',
        'created_at' => now(),
    ]);
    PageView::forceCreate([
        'url' => '/about',
        'ip' => '192.168.1.1',
        'created_at' => now(),
    ]);
    PageView::forceCreate([
        'url' => '/login',
        'ip' => '192.168.1.2',
        'created_at' => now(),
    ]);

    // Yesterday
    PageView::forceCreate([
        'url' => '/login',
        'ip' => '192.168.1.1',
        'created_at' => now()->subDays(2), // Use 2 days ago to guarantee it's not considered today in any timezone shift
    ]);

    $widget = new WebsiteAnalyticsWidget;
    $view = $widget->render();

    $periodsData = $view->getData()['widget']->periodsData;

    expect($periodsData)->toHaveKeys(['Today', '7 Days', '30 Days', 'This Year']);

    // Today stats
    $today = $periodsData['Today'];
    expect($today['metrics'][0]->value)->toBe('2'); // Visitors: 2 unique IPs today
    expect($today['metrics'][1]->value)->toBe('3'); // Page views: 3 today

    // 7 Days stats
    $sevenDays = $periodsData['7 Days'];
    expect($sevenDays['metrics'][1]->value)->toBe('4'); // 4 total views in last 7 days
});
