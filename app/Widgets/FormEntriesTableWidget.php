<?php

namespace App\Widgets;

use App\Models\Form;
use Illuminate\Support\Collection;

class FormEntriesTableWidget extends Widget
{
    public function __construct(
        public ?int $formId = null,
    ) {}

    public static function type(): string
    {
        return 'form_entries_table';
    }

    public static function zone(): string
    {
        return 'table';
    }

    public static function make(array $config): static
    {
        return new static(
            formId: isset($config['form_id']) ? (int) $config['form_id'] : null,
        );
    }

    public function label(): string
    {
        return 'Form Entries';
    }

    public function render()
    {
        $forms = Form::query()
            ->orderBy('position')
            ->orderBy('title')
            ->get(['id', 'title']);

        $form = $this->resolveForm($forms);

        $fields = collect($form?->fields ?? [])
            ->filter(fn ($field) => is_array($field) && filled($field['name'] ?? null))
            ->map(function ($field) {
                return [
                    'name' => $field['name'],
                    'label' => ! empty($field['column_name'])
                        ? $field['column_name']
                        : (! empty($field['label']) ? $field['label'] : str($field['name'])->replace('_', ' ')->title()->toString()),
                ];
            })
            ->take(6)
            ->values();

        $entries = $form
            ? $form->entries()->latest()->get()
            : collect();

        return view('admin.widgets.form-entries-table', [
            'forms' => $forms,
            'form' => $form,
            'fields' => $fields,
            'entries' => $entries,
            'selectedFormId' => $form?->id,
        ]);
    }

    /**
     * @param  Collection<int, Form>  $forms
     */
    protected function resolveForm(Collection $forms): ?Form
    {
        if ($this->formId) {
            $form = Form::query()->find($this->formId);

            if ($form) {
                return $form;
            }
        }

        $firstId = $forms->first()?->id;

        return $firstId ? Form::query()->find($firstId) : null;
    }
}
