<?php

use App\Models\Admin;
use App\Models\Form;
use App\Models\FormEntry;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('renders icon selector on form create page', function () {
    \Pest\Laravel\get(route('admin.forms.create'))
        ->assertStatus(200)
        ->assertSee('window.FA_ICONS =', false)
        ->assertSee('Choose icon');
});

it('creates a form with a selected icon', function () {
    post(route('admin.forms.store'), [
        'title' => 'Feedback Form',
        'icon' => 'fa-solid fa-comments',
        'description' => 'Give us feedback',
        'submit_text' => 'Submit',
        'success_message' => 'Thanks!',
    ])->assertRedirect(route('admin.forms.index'));

    $form = Form::where('title', 'Feedback Form')->first();
    expect($form)->not->toBeNull();
    expect($form->icon)->toBe('fa-solid fa-comments');
});

it('updates a form icon', function () {
    $form = Form::factory()->create([
        'title' => 'Contact Us',
        'icon' => 'fa-solid fa-envelope',
    ]);

    put(route('admin.forms.update', $form), [
        'title' => 'Contact Us Updated',
        'icon' => 'fa-solid fa-paper-plane',
        'description' => $form->description,
        'submit_text' => $form->submit_text,
        'success_message' => $form->success_message,
    ])->assertRedirect(route('admin.forms.index'));

    $form->refresh();
    expect($form->icon)->toBe('fa-solid fa-paper-plane');
});

it('renders the selected form icon in the admin sidebar', function () {
    Form::factory()->create([
        'title' => 'Survey Form',
        'icon' => 'fa-solid fa-poll',
    ]);

    \Pest\Laravel\get(route('admin.dashboard'))
        ->assertStatus(200)
        ->assertSee('fa-solid fa-poll');
});

it('displays a red circular badge for forms with entries and hides it when opening the form', function () {
    $form = Form::factory()->create(['title' => 'Inquiry Form']);
    FormEntry::factory()->create(['form_id' => $form->id]);

    // On dashboard, badge is visible in red circular styling
    \Pest\Laravel\get(route('admin.dashboard'))
        ->assertStatus(200)
        ->assertSee('bg-red-500', false)
        ->assertSee('rounded-full', false)
        ->assertSee('>1</span>', false);

    // On opening form 6 / form entries page, badge for this open form is gone
    \Pest\Laravel\get(route('admin.forms.entries', $form))
        ->assertStatus(200)
        ->assertDontSee('<span class="ml-auto flex size-5 shrink-0 items-center justify-center rounded-full bg-red-500 text-[11px] font-semibold leading-none text-white">1</span>', false);

    // When navigating back to another page (like dashboard), the badge should remain gone (since entries were marked as read)
    \Pest\Laravel\get(route('admin.dashboard'))
        ->assertStatus(200)
        ->assertDontSee('<span class="ml-auto flex size-5 shrink-0 items-center justify-center rounded-full bg-red-500 text-[11px] font-semibold leading-none text-white">1</span>', false);
});
