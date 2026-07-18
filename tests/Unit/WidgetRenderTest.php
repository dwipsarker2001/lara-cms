<?php

use App\Models\Admin;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

test('widget render returns html string in json', function () {
    $response = postJson(route('admin.widgets.render'), [
        'zone' => 'chart',
        'type' => 'website_analytics',
    ])->assertSuccessful();

    $response->assertJsonStructure(['html', 'type', 'label', 'image']);
    expect($response->json('html'))->toBeString();
    expect($response->json('html'))->toContain('<');
    expect($response->json('type'))->toBe('website_analytics');
});
