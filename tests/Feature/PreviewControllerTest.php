<?php

use App\Models\Admin;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('renders preview for sections with data', function () {
    postJson(route('admin.preview'), [
        'sections' => [
            [
                '_key' => 'abc-123',
                'name' => 'hero-banner',
                'enabled' => true,
                'data' => ['headline' => 'Hello World'],
            ],
        ],
    ])->assertOk()
        ->assertJsonStructure(['html']);
});

it('accepts an empty sections array and returns empty html', function () {
    postJson(route('admin.preview'), [
        'sections' => [],
    ])->assertOk()
        ->assertJson(['html' => '']);
});
