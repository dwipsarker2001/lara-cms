@extends('admin.layout')

@section('title', 'Add ' . $form->title)

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <form method="POST" action="{{ route('admin.forms.entries.store', $form) }}" enctype="multipart/form-data">
            @csrf

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <div>
                    <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                        <span class="flex size-6 shrink-0 items-center justify-center text-text-muted">
                            @if($form->icon)
                                <i class="{{ $form->icon }} text-lg"></i>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                    <line x1="3" y1="9" x2="21" y2="9" />
                                    <line x1="9" y1="21" x2="9" y2="9" />
                                </svg>
                            @endif
                        </span>
                        Add {{ $form->title }}
                    </h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.forms.entries', $form) }}"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200"
                    >
                        Back
                    </a>
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        Create Entry
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Submission Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Fill in the fields defined for this form.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-4 sm:px-[18px] py-5 space-y-5">
                        @forelse ($form->fields ?? [] as $field)
                            @php
                                $fieldName = $field['name'] ?? '';
                                $fieldType = $field['type'] ?? 'text';
                                $fieldLabel = $field['column_name'] ?? ($field['label'] ?? str_replace('_', ' ', $fieldName));
                                $isRequired = !empty($field['required']);
                                $placeholder = $field['placeholder'] ?? ('Enter ' . strtolower($fieldLabel));
                                $defaultValue = $field['default_value'] ?? '';

                                $options = [];
                                if (!empty($field['options'])) {
                                    if (is_array($field['options'])) {
                                        $options = $field['options'];
                                    } elseif (is_string($field['options'])) {
                                        $options = array_filter(array_map('trim', explode(',', $field['options'])));
                                    }
                                }
                            @endphp

                            @if($fieldName)
                                <div class="min-w-0 flex flex-col gap-2">
                                    <label class="text-sm font-medium text-text-primary" for="field_{{ $fieldName }}">
                                        {{ $fieldLabel }}
                                        @if ($isRequired)
                                            <span class="text-red-600">*</span>
                                        @endif
                                    </label>

                                    {{-- Textarea --}}
                                    @if ($fieldType === 'textarea')
                                        <textarea
                                            id="field_{{ $fieldName }}"
                                            name="data[{{ $fieldName }}]"
                                            rows="4"
                                            placeholder="{{ $placeholder }}"
                                            @if($isRequired) required @endif
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                        >{{ old('data.' . $fieldName, $defaultValue) }}</textarea>

                                    {{-- Select Dropdown --}}
                                    @elseif ($fieldType === 'select')
                                        <select
                                            id="field_{{ $fieldName }}"
                                            name="data[{{ $fieldName }}]"
                                            @if($isRequired) required @endif
                                            class="w-full block bg-content-bg border border-content-border text-text-primary shadow-sm text-sm rounded-lg px-3 py-2 h-10 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary cursor-pointer"
                                        >
                                            <option value="">Choose an option...</option>
                                            @foreach($options as $opt)
                                                <option value="{{ $opt }}" @selected(old('data.' . $fieldName, $defaultValue) == $opt)>{{ $opt }}</option>
                                            @endforeach
                                        </select>

                                    {{-- Checkboxes --}}
                                    @elseif ($fieldType === 'checkbox')
                                        @if(count($options) > 0)
                                            <div class="flex flex-col gap-2 pt-1">
                                                @foreach($options as $opt)
                                                    @php
                                                        $oldVal = old('data.' . $fieldName);
                                                        $checked = is_array($oldVal)
                                                            ? in_array($opt, $oldVal)
                                                            : ($defaultValue === $opt || (is_array($defaultValue) && in_array($opt, $defaultValue)));
                                                    @endphp
                                                    <label class="flex items-center gap-2.5 cursor-pointer text-sm text-text-primary">
                                                        <input
                                                            type="checkbox"
                                                            name="data[{{ $fieldName }}][]"
                                                            value="{{ $opt }}"
                                                            @checked($checked)
                                                            class="size-4 rounded border-content-border text-primary focus:ring-primary/30 cursor-pointer"
                                                        >
                                                        <span>{{ $opt }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <label class="flex items-center gap-2.5 cursor-pointer text-sm text-text-primary pt-1">
                                                <input type="hidden" name="data[{{ $fieldName }}]" value="0">
                                                <input
                                                    type="checkbox"
                                                    id="field_{{ $fieldName }}"
                                                    name="data[{{ $fieldName }}]"
                                                    value="1"
                                                    @checked(old('data.' . $fieldName, $defaultValue) == '1' || old('data.' . $fieldName, $defaultValue) === true)
                                                    class="size-4 rounded border-content-border text-primary focus:ring-primary/30 cursor-pointer"
                                                >
                                                <span class="text-sm text-text-muted">{{ $placeholder ?: 'Enable this option' }}</span>
                                            </label>
                                        @endif

                                    {{-- Radio Buttons --}}
                                    @elseif ($fieldType === 'radio')
                                        <div class="flex flex-col gap-2 pt-1">
                                            @foreach($options as $opt)
                                                <label class="flex items-center gap-2.5 cursor-pointer text-sm text-text-primary">
                                                    <input
                                                        type="radio"
                                                        name="data[{{ $fieldName }}]"
                                                        value="{{ $opt }}"
                                                        @checked(old('data.' . $fieldName, $defaultValue) == $opt)
                                                        @if($isRequired) required @endif
                                                        class="size-4 border-content-border text-primary focus:ring-primary/30 cursor-pointer"
                                                    >
                                                    <span>{{ $opt }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                    {{-- Date Picker --}}
                                    @elseif ($fieldType === 'date')
                                        <input
                                            id="field_{{ $fieldName }}"
                                            type="date"
                                            name="data[{{ $fieldName }}]"
                                            value="{{ old('data.' . $fieldName, $defaultValue) }}"
                                            @if($isRequired) required @endif
                                            class="w-full block bg-content-bg border border-content-border text-text-primary shadow-sm text-sm rounded-lg px-3 py-2 h-10 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                        >

                                    {{-- Time Picker --}}
                                    @elseif ($fieldType === 'time')
                                        <input
                                            id="field_{{ $fieldName }}"
                                            type="time"
                                            name="data[{{ $fieldName }}]"
                                            value="{{ old('data.' . $fieldName, $defaultValue) }}"
                                            @if($isRequired) required @endif
                                            class="w-full block bg-content-bg border border-content-border text-text-primary shadow-sm text-sm rounded-lg px-3 py-2 h-10 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                        >

                                    {{-- File Upload --}}
                                    @elseif ($fieldType === 'file')
                                        <div class="flex items-center gap-3">
                                            <input
                                                id="field_{{ $fieldName }}"
                                                type="file"
                                                name="data[{{ $fieldName }}]"
                                                @if($isRequired) required @endif
                                                class="block w-full text-sm text-text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20 file:cursor-pointer cursor-pointer border border-content-border rounded-lg bg-content-bg shadow-sm"
                                            >
                                        </div>

                                    {{-- Color Picker --}}
                                    @elseif ($fieldType === 'color')
                                        <div class="flex items-center gap-2.5">
                                            <input
                                                type="color"
                                                id="field_picker_{{ $fieldName }}"
                                                value="{{ old('data.' . $fieldName, $defaultValue ?: '#000000') }}"
                                                oninput="document.getElementById('field_{{ $fieldName }}').value = this.value"
                                                class="size-10 rounded-lg border border-content-border p-1 bg-content-bg cursor-pointer"
                                            >
                                            <input
                                                id="field_{{ $fieldName }}"
                                                type="text"
                                                name="data[{{ $fieldName }}]"
                                                value="{{ old('data.' . $fieldName, $defaultValue) }}"
                                                placeholder="#000000"
                                                oninput="document.getElementById('field_picker_{{ $fieldName }}').value = this.value"
                                                @if($isRequired) required @endif
                                                class="w-full block font-mono bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-10 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                            >
                                        </div>

                                    {{-- General Inputs (text, number, email, phone, tel, url, password, etc.) --}}
                                    @else
                                        @php
                                            $inputType = match($fieldType) {
                                                'number' => 'number',
                                                'email' => 'email',
                                                'phone', 'tel' => 'tel',
                                                'url' => 'url',
                                                'password' => 'password',
                                                default => 'text'
                                            };
                                        @endphp
                                        <input
                                            id="field_{{ $fieldName }}"
                                            type="{{ $inputType }}"
                                            name="data[{{ $fieldName }}]"
                                            value="{{ old('data.' . $fieldName, $defaultValue) }}"
                                            placeholder="{{ $placeholder }}"
                                            @if($isRequired) required @endif
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-10 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                        >
                                    @endif

                                    @error('data.' . $fieldName) <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        @empty
                            <div class="py-8 text-center text-text-muted text-sm">
                                No fields have been configured for this form yet. <a href="{{ route('admin.forms.editor', $form) }}" class="text-primary hover:underline font-medium">Add fields in the Form Editor</a>.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
