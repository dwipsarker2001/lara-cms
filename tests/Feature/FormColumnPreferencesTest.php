<?php

use App\Models\Admin;
use App\Models\Form;
use App\Models\WidgetLayout;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('saves form column visibility preferences for the admin', function () {
    $form = Form::factory()->create([
        'title' => 'Survey Form',
        'fields' => [
            ['_key' => 'k1', 'type' => 'text', 'label' => 'Name', 'name' => 'name'],
            ['_key' => 'k2', 'type' => 'email', 'label' => 'Email', 'name' => 'email'],
        ],
    ]);

    $preferences = [
        'id' => true,
        'name' => false,
        'email' => true,
        'created' => false,
        'actions' => true,
    ];

    postJson(route('admin.forms.save-columns', $form), [
        'columns' => $preferences,
    ])->assertSuccessful()->assertJson(['message' => 'Column preferences saved.']);

    $layoutRecord = WidgetLayout::where('admin_id', $this->admin->id)->first();
    expect($layoutRecord)->not->toBeNull();
    expect($layoutRecord->layout['form_columns'][$form->id])->toBe($preferences);
});

it('loads saved column preferences on the entries page', function () {
    $form = Form::factory()->create([
        'title' => 'Feedback Form',
        'fields' => [
            ['_key' => 'k1', 'type' => 'text', 'label' => 'Feedback', 'name' => 'feedback'],
        ],
    ]);

    WidgetLayout::create([
        'admin_id' => $this->admin->id,
        'layout' => [
            'form_columns' => [
                $form->id => [
                    'id' => true,
                    'feedback' => false,
                    'created' => true,
                    'actions' => true,
                ],
            ],
        ],
    ]);

    get(route('admin.forms.entries', $form))
        ->assertSuccessful()
        ->assertSee('Feedback Form')
        ->assertSee('saveColumnPreferences', false)
        ->assertSee('style="display: none;"', false);
});
