<?php

use App\Models\Admin;
use App\Models\Form;
use App\Models\FormEntry;
use App\Widgets\FormStatWidget;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('renders form stat widget with all forms entries count and analytics', function () {
    FormEntry::query()->delete();
    Form::query()->delete();

    $form1 = Form::factory()->create(['title' => 'Contact Form']);
    $form2 = Form::factory()->create(['title' => 'Feedback Form']);

    FormEntry::factory()->count(3)->create(['form_id' => $form1->id]);
    FormEntry::factory()->count(2)->create(['form_id' => $form2->id]);

    $widget = new FormStatWidget;
    $view = $widget->render();

    $data = $view->getData();
    expect($data['widget']->value)->toBe('3');
    expect($data['widget']->label)->toBe('Total Contact Form');
    expect($data['selectedFormTitle'])->toBe('Contact Form');
});

it('filters form stat widget by selected form id', function () {
    FormEntry::query()->delete();
    Form::query()->delete();

    $form1 = Form::factory()->create(['title' => 'Contact Form']);
    $form2 = Form::factory()->create(['title' => 'Feedback Form']);

    FormEntry::factory()->count(4)->create(['form_id' => $form1->id]);
    FormEntry::factory()->count(1)->create(['form_id' => $form2->id]);

    $widget = new FormStatWidget(formId: $form1->id);
    $view = $widget->render();

    $data = $view->getData();
    expect($data['widget']->value)->toBe('4');
    expect($data['widget']->label)->toBe('Total Contact Form');
    expect($data['selectedFormId'])->toBe($form1->id);
});

it('renders form stat widget through AJAX render endpoint', function () {
    $form = Form::factory()->create(['title' => 'Newsletter Form']);
    FormEntry::factory()->count(2)->create(['form_id' => $form->id]);

    $response = $this->postJson(route('admin.widgets.render'), [
        'zone' => 'grid',
        'type' => 'form_stat',
        'form_id' => $form->id,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['html', 'type', 'label']);
    expect($response->json('type'))->toBe('form_stat');
});
