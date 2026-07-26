# Dynamic Form Builder and Checkout Form Integration

## Overview
Enable full customization of input fields in the Form Builder (`/admin/forms/{form}/editor`) including Field Key (`name`), Label (`label`), Placeholder (`placeholder`), Custom Error Message (`error_message`), and Required status.
Automatically render these input fields dynamically in `/resources/views/blocks/checkout-form.blade.php` with validation and error handling on submission.

## 1. Form Editor (`resources/views/admin/forms/editor.blade.php`)
- Update `fieldSchemas` in Alpine.js `formEditor()` component so that all field types (`text`, `email`, `phone`, `number`, `textarea`, `select`, `checkbox`, `radio`, `date`, `file`) include:
  - `name`: Field Key (name attribute used during HTTP POST submission)
  - `label`: Label text displayed above input
  - `placeholder`: Placeholder text (where applicable)
  - `error_message`: Custom validation error message
  - `required`: Boolean flag indicating if input is required
- Ensure `addField()` initializes `name`, `label`, `placeholder`, `error_message`, and `required`.

## 2. Dynamic Input Rendering (`resources/views/blocks/checkout-form.blade.php`)
- Check if `$selectedForm` exists and `$selectedForm->fields` is non-empty.
- When dynamic fields are present:
  - Loop over `$selectedForm->fields` and render inputs matching their `type`:
    - `text`, `email`, `phone`, `number`, `date`, `file`
    - `textarea`
    - `select` (with `options`)
    - `checkbox` & `radio` (with `options`)
  - Set `name="{{ $field['name'] }}"`, `placeholder="{{ $field['placeholder'] ?? '' }}"`, `value="{{ old($field['name']) }}"`.
  - Append error block `@error($field['name']) <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p> @enderror`.
- If no form/fields are configured, fall back to default checkout inputs.

## 3. Form Submission & Validation (`app/Http/Controllers/PublicFormController.php`)
- Validate submitted form inputs using dynamic rules generated from `$selectedForm->fields`.
- Apply custom error message (`$field['error_message']`) when validation fails.
- Save entry data into `FormEntry->data`.

## 4. Tests (`tests/Feature/FormEditorTest.php` & `tests/Feature/PublicFormSubmissionTest.php`)
- Test saving fields with custom `name` and `error_message` in Form Editor.
- Test rendering dynamic fields in `checkout-form.blade.php`.
- Test public submission with custom validation error messages.
