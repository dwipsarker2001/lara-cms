<?php

use App\Models\Admin;
use App\Models\Setting;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('renders settings page with custom inputs builder and add input button', function () {
    get(route('admin.settings'))
        ->assertSuccessful()
        ->assertSee('Add Input')
        ->assertSee('General Settings')
        ->assertSee('name="custom_fields"', false);
});

it('can store and update custom fields schema and values in settings', function () {
    $customFields = [
        [
            'title' => 'Support Phone',
            'description' => 'Direct phone number displayed on header',
            'type' => 'text',
            'template' => 'support_phone',
        ],
        [
            'title' => 'Announcement Banner',
            'description' => 'Top site announcement message',
            'type' => 'textarea',
            'template' => 'announcement_banner',
        ],
        [
            'title' => 'Maintenance Mode Notice',
            'description' => 'Toggle maintenance notice',
            'type' => 'toggle',
            'template' => 'enable_notice',
        ],
    ];

    $customValues = [
        'support_phone' => '+1 (555) 019-2834',
        'announcement_banner' => 'Welcome to our brand new store!',
        'enable_notice' => '1',
    ];

    $response = put(route('admin.settings.update'), [
        'currency' => 'USD',
        'custom_fields' => json_encode($customFields),
        'custom_values' => $customValues,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $settings = Setting::firstOrCreate(['id' => 1]);

    expect($settings->custom_fields)->toBeArray()
        ->toHaveCount(3)
        ->and($settings->custom_fields[0]['template'])->toBe('support_phone')
        ->and($settings->custom_values)->toBeArray()
        ->and($settings->custom_values['support_phone'])->toBe('+1 (555) 019-2834')
        ->and($settings->custom_values['announcement_banner'])->toBe('Welcome to our brand new store!');

    // Test helper methods
    expect(Setting::getCustom('support_phone'))->toBe('+1 (555) 019-2834')
        ->and(Setting::getCustom('non_existent', 'default_val'))->toBe('default_val')
        ->and($settings->getCustomValue('announcement_banner'))->toBe('Welcome to our brand new store!');
});

it('persists complex custom fields such as location and colors in settings', function () {
    $customFields = [
        [
            'title' => 'Brand Color',
            'type' => 'color',
            'template' => 'brand_color',
        ],
        [
            'title' => 'Headquarters Location',
            'type' => 'location',
            'template' => 'hq_location',
            'enable_country' => true,
            'enable_state' => true,
            'enable_city' => true,
        ],
    ];

    $customValues = [
        'brand_color' => '#6366f1',
        'hq_location' => [
            'country' => 'United States',
            'state' => 'California',
            'city' => 'San Francisco',
        ],
    ];

    put(route('admin.settings.update'), [
        'custom_fields' => json_encode($customFields),
        'custom_values' => $customValues,
    ])->assertRedirect();

    expect(Setting::getCustom('brand_color'))->toBe('#6366f1')
        ->and(Setting::getCustom('hq_location'))->toBe([
            'country' => 'United States',
            'state' => 'California',
            'city' => 'San Francisco',
        ]);
});

it('persists reordered custom fields correctly', function () {
    $fieldA = ['title' => 'Field A', 'type' => 'text', 'template' => 'field_a'];
    $fieldB = ['title' => 'Field B', 'type' => 'text', 'template' => 'field_b'];
    $fieldC = ['title' => 'Field C', 'type' => 'text', 'template' => 'field_c'];

    // Save initial order: A, B, C
    put(route('admin.settings.update'), [
        'custom_fields' => json_encode([$fieldA, $fieldB, $fieldC]),
        'custom_values' => ['field_a' => '1', 'field_b' => '2', 'field_c' => '3'],
    ])->assertRedirect();

    $settings = Setting::first();
    expect($settings->custom_fields[0]['template'])->toBe('field_a')
        ->and($settings->custom_fields[1]['template'])->toBe('field_b')
        ->and($settings->custom_fields[2]['template'])->toBe('field_c');

    // Save reordered fields: C, A, B
    put(route('admin.settings.update'), [
        'custom_fields' => json_encode([$fieldC, $fieldA, $fieldB]),
        'custom_values' => ['field_a' => '1', 'field_b' => '2', 'field_c' => '3'],
    ])->assertRedirect();

    $settings->refresh();
    expect($settings->custom_fields[0]['template'])->toBe('field_c')
        ->and($settings->custom_fields[1]['template'])->toBe('field_a')
        ->and($settings->custom_fields[2]['template'])->toBe('field_b');
});

it('can auto-save reordered custom fields via AJAX patch request', function () {
    $fieldA = ['title' => 'Field 1', 'type' => 'text', 'template' => 'f1'];
    $fieldB = ['title' => 'Field 2', 'type' => 'text', 'template' => 'f2'];

    Setting::firstOrCreate(['id' => 1])->update([
        'custom_fields' => [$fieldA, $fieldB],
    ]);

    \Pest\Laravel\patch(route('admin.settings.reorder_custom_fields'), [
        'custom_fields' => [$fieldB, $fieldA],
    ])->assertNoContent();

    $settings = Setting::first();
    expect($settings->custom_fields[0]['template'])->toBe('f2')
        ->and($settings->custom_fields[1]['template'])->toBe('f1');
});
