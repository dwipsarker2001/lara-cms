# Dynamic Form Builder and Checkout Form Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Allow editing input field keys (`name`) and custom error messages (`error_message`) in the Form Builder editor and dynamically render inputs in `checkout-form.blade.php` with submit and validation support.

**Architecture:** Extend Alpine.js `fieldSchemas` in `resources/views/admin/forms/editor.blade.php` to include `name` and `error_message` schema properties. Update `resources/views/blocks/checkout-form.blade.php` to dynamically iterate through `$selectedForm->fields` when available, displaying appropriate input fields and error messages.

**Tech Stack:** PHP 8.5, Laravel 13, Blade, Alpine.js, Tailwind CSS v3, Pest v4.

## Global Constraints
- Laravel 13 & PHP 8.5 strictly.
- Pest for testing. Run tests with `php artisan test --compact --filter=...`.
- Format modified PHP files with `vendor/bin/pint --dirty --format agent`.

---

### Task 1: Add `name` and `error_message` schema properties to Form Editor

**Files:**
- Modify: `resources/views/admin/forms/editor.blade.php:275-325,355-388`
- Test: `tests/Feature/FormEditorTest.php`

**Interfaces:**
- Consumes: Form model `fields` array structure.
- Produces: Form `fields` array where every element contains `_key`, `type`, `label`, `name`, `placeholder`, `error_message`, `required`.

- [ ] **Step 1: Write the failing test for custom name and error_message in FormEditorTest**

Add the following test to `tests/Feature/FormEditorTest.php`:
```php
it('saves field key and custom error message', function () {
    $form = Form::factory()->create();

    $fields = [
        [
            '_key' => 'key-custom-1',
            'type' => 'text',
            'label' => 'Customer Name',
            'name' => 'cust_name',
            'placeholder' => 'Enter your full name',
            'error_message' => 'Customer Name is required!',
            'required' => true,
        ],
    ];

    patch(route('admin.forms.update-fields', $form), [
        'fields' => $fields,
    ])->assertSuccessful()->assertJson(['message' => 'Form fields saved.']);

    $form->refresh();
    expect($form->fields[0]['name'])->toBe('cust_name');
    expect($form->fields[0]['error_message'])->toBe('Customer Name is required!');
});
```

- [ ] **Step 2: Run test to verify it passes/fails**

Run: `php artisan test --compact --filter=FormEditorTest`
Expected: PASS for backend update, but we need to check frontend schema property setup.

- [ ] **Step 3: Update `editor.blade.php` fieldSchemas & addField defaults**

In `resources/views/admin/forms/editor.blade.php`:
Update `fieldSchemas` in JS so every field type has:
- `label` (Label)
- `name` (Field Key / Name)
- `placeholder` (Placeholder - if text/email/phone/number/textarea/date)
- `error_message` (Custom Error Message)
- `required` (Required toggle)

Also in `addField(name)`, initialize `error_message: ''`.

- [ ] **Step 4: Run tests to verify**

Run: `php artisan test --compact --filter=FormEditorTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/forms/editor.blade.php tests/Feature/FormEditorTest.php
git commit -m "feat: add field key and custom error message settings to form editor"
```

---

### Task 2: Dynamic Input Rendering in `checkout-form.blade.php`

**Files:**
- Modify: `resources/views/blocks/checkout-form.blade.php:116-229`
- Test: `tests/Feature/PublicFormSubmissionTest.php`

**Interfaces:**
- Consumes: `$selectedForm->fields` (array of field schemas with `type`, `label`, `name`, `placeholder`, `error_message`, `required`, `options`).
- Produces: Dynamic Blade inputs rendered dynamically inside the `<form>` element.

- [ ] **Step 1: Write failing feature test for dynamic field rendering and form submission**

Add the following test in `tests/Feature/PublicFormSubmissionTest.php`:

```php
it('renders dynamic form fields from selected form in checkout-form and submits correctly with custom error message', function () {
    $form = Form::factory()->create([
        'title' => 'Custom Dynamic Form',
        'submit_text' => 'Submit Order',
        'success_message' => 'Success!',
        'fields' => [
            [
                '_key' => 'f1',
                'type' => 'text',
                'label' => 'Traveler Full Name',
                'name' => 'traveler_name',
                'placeholder' => 'John Doe',
                'error_message' => 'Please provide traveler full name',
                'required' => true,
            ],
            [
                '_key' => 'f2',
                'type' => 'email',
                'label' => 'Traveler Email',
                'name' => 'traveler_email',
                'placeholder' => 'john@example.com',
                'error_message' => 'Please provide valid traveler email',
                'required' => true,
            ],
        ],
    ]);

    // Render block view
    $view = $this->view('blocks.checkout-form', [
        'data' => [
            'formId' => $form->id,
            'formTitle' => 'Traveler Details',
        ],
        'errors' => new Illuminate\Support\ViewErrorBag,
    ]);

    $view->assertSee('Traveler Full Name');
    $view->assertSee('name="traveler_name"', false);
    $view->assertSee('Traveler Email');
    $view->assertSee('name="traveler_email"', false);

    // Validation failure test
    $failResponse = $this->post(route('forms.public-submit', $form), []);
    $failResponse->assertSessionHasErrors([
        'traveler_name' => 'Please provide traveler full name',
        'traveler_email' => 'Please provide valid traveler email',
    ]);

    // Successful submit test
    $successResponse = $this->post(route('forms.public-submit', $form), [
        'traveler_name' => 'Alice Wonder',
        'traveler_email' => 'alice@example.com',
    ]);

    $successResponse->assertRedirect();
    $entry = App\Models\FormEntry::where('form_id', $form->id)->first();
    expect($entry)->not->toBeNull();
    expect($entry->data['traveler_name'])->toBe('Alice Wonder');
    expect($entry->data['traveler_email'])->toBe('alice@example.com');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=PublicFormSubmissionTest`
Expected: FAIL (missing fields in rendered view or error message mismatch).

- [ ] **Step 3: Update `checkout-form.blade.php` to render dynamic form inputs**

Update `<form>` in `resources/views/blocks/checkout-form.blade.php`:
If `$selectedForm && !empty($selectedForm->fields)`:
Iterate over `$selectedForm->fields as $field`:
- `$fieldKey = $field['name'] ?? ('field_'.$loop->index);`
- `$fieldType = $field['type'] ?? 'text';`
- `$fieldLabel = $field['label'] ?? '';`
- `$fieldPlaceholder = $field['placeholder'] ?? '';`
- `$fieldRequired = !empty($field['required']);`
- `$fieldOptions = $field['options'] ?? [];`

Render appropriate inputs based on `$fieldType`:
- `text`, `email`, `phone` / `tel`, `number`, `date`: `<input type="{{ $fieldType === 'phone' ? 'tel' : $fieldType }}" name="{{ $fieldKey }}" value="{{ old($fieldKey) }}" placeholder="{{ $fieldPlaceholder }}" {{ $fieldRequired ? 'required' : '' }} ...>`
- `textarea`: `<textarea name="{{ $fieldKey }}" placeholder="{{ $fieldPlaceholder }}" {{ $fieldRequired ? 'required' : '' }}>{{ old($fieldKey) }}</textarea>`
- `select`: `<select name="{{ $fieldKey }}">...`
- `checkbox` / `radio`: `<input type="{{ $fieldType }}" name="{{ $fieldKey }}[]" ...>`
- `file`: `<input type="file" name="{{ $fieldKey }}">`

Under every dynamic field input:
```blade
@error($fieldKey)
    <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
@enderror
```

If `$selectedForm` has no fields or is empty, render the existing default form inputs.

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter=PublicFormSubmissionTest`
Run: `php artisan test --compact --filter=FormEditorTest`
Expected: PASS

- [ ] **Step 5: Run Pint code formatter**

Run: `vendor/bin/pint --dirty --format agent`

- [ ] **Step 6: Commit**

```bash
git add resources/views/blocks/checkout-form.blade.php tests/Feature/PublicFormSubmissionTest.php
git commit -m "feat: render dynamic form fields and custom error messages in checkout form"
```
