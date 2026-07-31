<?php

use App\Models\Admin;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\WidgetLayout;
use App\Widgets\FormEntriesTableWidget;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('renders form entries for the selected form on the dashboard', function () {
    $contact = Form::factory()->create([
        'title' => 'Contact Form',
        'fields' => [
            ['_key' => '1', 'type' => 'text', 'label' => 'Full Name', 'name' => 'name'],
            ['_key' => '2', 'type' => 'email', 'label' => 'Email', 'name' => 'email'],
        ],
    ]);

    Form::factory()->create(['title' => 'Newsletter']);

    FormEntry::factory()->create([
        'form_id' => $contact->id,
        'data' => [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
        ],
    ]);

    WidgetLayout::query()->create([
        'admin_id' => $this->admin->id,
        'layout' => [
            'table' => [
                'order' => ['form_entries_table'],
                'hidden' => [],
                'form_id' => $contact->id,
            ],
        ],
    ]);

    get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Contact Form', false)
        ->assertSee('Ada Lovelace', false)
        ->assertSee('ada@example.com', false)
        ->assertSee('Full Name', false);
});

it('defaults to the first form when no form is selected', function () {
    $first = Form::factory()->create([
        'title' => 'Alpha Form',
        'position' => 1,
        'fields' => [
            ['_key' => '1', 'type' => 'text', 'label' => 'Name', 'name' => 'name'],
        ],
    ]);
    Form::factory()->create(['title' => 'Beta Form', 'position' => 2]);

    FormEntry::factory()->create([
        'form_id' => $first->id,
        'data' => ['name' => 'First Entry'],
    ]);

    get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Alpha Form', false)
        ->assertSee('First Entry', false);
});

it('saves the selected form id in the widget layout', function () {
    $form = Form::factory()->create(['title' => 'Booking Form']);

    postJson(route('admin.widgets.layout'), [
        'table' => [
            'order' => ['form_entries_table'],
            'hidden' => [],
            'form_id' => $form->id,
        ],
    ])->assertSuccessful()->assertJson(['saved' => true]);

    $layout = WidgetLayout::query()->where('admin_id', $this->admin->id)->first();

    expect($layout)->not->toBeNull()
        ->and($layout->layout['table']['form_id'])->toBe($form->id)
        ->and($layout->layout['table']['order'])->toBe(['form_entries_table']);
});

it('renders the selected form entries via the widgets render endpoint', function () {
    $form = Form::factory()->create([
        'title' => 'Support Form',
        'fields' => [
            ['_key' => '1', 'type' => 'text', 'label' => 'Subject', 'name' => 'subject'],
        ],
    ]);

    FormEntry::factory()->create([
        'form_id' => $form->id,
        'data' => ['subject' => 'Need help logging in'],
    ]);

    postJson(route('admin.widgets.render'), [
        'zone' => 'table',
        'type' => 'form_entries_table',
        'form_id' => $form->id,
    ])
        ->assertSuccessful()
        ->assertJsonPath('type', 'form_entries_table')
        ->assertJsonPath('label', 'Form Entries — Support Form')
        ->assertSee('Support Form', false)
        ->assertSee('Need help logging in', false);
});

it('builds the form entries widget with a form id', function () {
    $form = Form::factory()->create(['title' => 'Widget Form']);

    $html = (string) FormEntriesTableWidget::make(['form_id' => $form->id])->render();

    expect($html)->toContain('Widget Form');
});
