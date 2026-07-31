@extends('admin.layout')

@section('title', 'Add ' . $form->title)

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <form method="POST" action="{{ route('admin.forms.entries.store', $form) }}">
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
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-4 sm:px-[18px] py-5 space-y-4">
                        @foreach ($form->fields ?? [] as $field)
                            @php
                                $fieldName = $field['name'] ?? '';
                                $fieldType = $field['type'] ?? 'text';
                                $fieldLabel = $field['column_name'] ?? ($field['label'] ?? str_replace('_', ' ', $fieldName));
                                $isRequired = !empty($field['required']);
                            @endphp
                            @if($fieldName)
                                <div class="min-w-0 flex flex-col gap-2">
                                    <label class="text-sm font-medium text-text-primary" for="field_{{ $fieldName }}">
                                        {{ $fieldLabel }}
                                        @if ($isRequired)
                                            <span class="text-red-600">*</span>
                                        @endif
                                    </label>
                                    @if ($fieldType === 'textarea')
                                        <textarea
                                            id="field_{{ $fieldName }}"
                                            name="data[{{ $fieldName }}]"
                                            rows="4"
                                            placeholder="Enter {{ strtolower($fieldLabel) }}"
                                            @if($isRequired) required @endif
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                        >{{ old('data.' . $fieldName) }}</textarea>
                                    @else
                                        <input
                                            id="field_{{ $fieldName }}"
                                            type="{{ $fieldType === 'number' ? 'number' : ($fieldType === 'email' ? 'email' : 'text') }}"
                                            name="data[{{ $fieldName }}]"
                                            value="{{ old('data.' . $fieldName) }}"
                                            placeholder="Enter {{ strtolower($fieldLabel) }}"
                                            @if($isRequired) required @endif
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                        >
                                    @endif
                                    @error('data.' . $fieldName) <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
