<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\WidgetLayout;
use App\Support\FormFieldTypes;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class FormController extends Controller
{
    public function index()
    {
        return view('admin.forms.index', ['forms' => Form::orderBy('position')->get()]);
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'submit_text' => 'required|string|max:255',
            'success_message' => 'required|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:500',
        ]);
        $data['per_page'] = $data['per_page'] ?? 15;
        $data['position'] = Form::max('position') + 1;
        Form::create($data);

        return redirect()->route('admin.forms.index')->with('success', 'Form created successfully.');
    }

    public function edit(Form $form)
    {
        return view('admin.forms.edit', ['form' => $form]);
    }

    public function update(Request $request, Form $form)
    {
        $form->update($request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'submit_text' => 'required|string|max:255',
            'success_message' => 'required|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:500',
        ]));

        return redirect()->route('admin.forms.index')->with('success', 'Form updated successfully.');
    }

    public function destroy(Form $form)
    {
        $form->delete();

        return redirect()->route('admin.forms.index')->with('success', 'Form deleted successfully.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->form_ids ?? [] as $i => $id) {
            Form::where('id', $id)->update(['position' => $i]);
        }

        return response()->noContent();
    }

    public function entries(Form $form)
    {
        if (Schema::hasTable('form_entries') && Schema::hasColumn('form_entries', 'status')) {
            $form->entries()->where('status', 1)->update(['status' => 0]);
        }

        $perPage = (int) ($form->per_page ?? 15);
        if ($perPage < 1) {
            $perPage = 15;
        }

        $entries = Schema::hasTable('form_entries')
            ? $form->entries()->latest()->paginate($perPage)
            : new LengthAwarePaginator([], 0, $perPage);

        $savedColumns = WidgetLayout::where('admin_id', auth('admin')->id())
            ->value('layout')['form_columns'][$form->id] ?? null;

        return view('admin.forms.entries', compact('form', 'entries', 'savedColumns'));
    }

    public function entryJson(Form $form, FormEntry $entry)
    {
        abort_if($entry->form_id !== $form->id, 404);

        return response()->json($entry);
    }

    public function export(Form $form)
    {
        if (! Schema::hasTable('form_entries')) {
            return redirect()->route('admin.forms.entries', $form);
        }

        $entries = $form->entries()->latest()->get();

        $fields = collect($form->fields ?? [])->mapWithKeys(function ($field) {
            $name = $field['name'] ?? null;
            if (! $name) {
                return [];
            }

            $column = ! empty($field['column_name']) ? $field['column_name'] : (! empty($field['label']) ? $field['label'] : $name);

            return [$name => $column];
        });

        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['ID', 'Submitted', ...$fields->values()->toArray()]);

        foreach ($entries as $entry) {
            $row = [$entry->id, $entry->created_at->format('Y-m-d H:i:s')];
            foreach ($fields->keys() as $name) {
                $row[] = $entry->data[$name] ?? '';
            }
            fputcsv($csv, $row);
        }

        rewind($csv);
        $output = stream_get_contents($csv);
        fclose($csv);

        return response($output, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$form->title.'-entries.csv"',
        ]);
    }

    public function editor(Form $form)
    {
        return view('admin.forms.editor', [
            'form' => $form,
            'fieldList' => FormFieldTypes::catalog(),
        ]);
    }

    public function updateFields(Request $request, Form $form)
    {
        $request->validate([
            'fields' => ['present', 'array'],
            'fields.*._key' => ['required', 'string'],
            'fields.*.type' => ['required', 'string'],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.name' => ['required', 'string', 'max:255'],
        ]);

        $form->update(['fields' => $request->fields]);

        return response()->json(['message' => 'Form fields saved.']);
    }

    public function updateEntry(Request $request, Form $form, FormEntry $entry)
    {
        abort_if($entry->form_id !== $form->id, 404);

        $entry->update([
            'data' => $request->input('data', []),
        ]);

        return redirect()->route('admin.forms.entries', $form)->with('success', 'Submission updated successfully.');
    }

    public function createEntry(Form $form)
    {
        return view('admin.forms.entries_create', compact('form'));
    }

    public function storeEntry(Request $request, Form $form)
    {
        FormEntry::create([
            'form_id' => $form->id,
            'data' => $request->input('data', []),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 0,
        ]);

        return redirect()->route('admin.forms.entries', $form)->with('success', 'Submission created successfully.');
    }

    public function destroyEntry(Form $form, FormEntry $entry)
    {
        abort_if($entry->form_id !== $form->id, 404);

        $entry->delete();

        return redirect()->route('admin.forms.entries', $form)->with('success', 'Submission deleted successfully.');
    }

    public function saveColumns(Request $request, Form $form)
    {
        $validated = $request->validate([
            'columns' => ['required', 'array'],
        ]);

        $layoutRecord = WidgetLayout::firstOrCreate(
            ['admin_id' => auth('admin')->id()],
            ['layout' => []]
        );

        $layout = $layoutRecord->layout ?? [];
        $layout['form_columns'][$form->id] = $validated['columns'];

        $layoutRecord->update(['layout' => $layout]);

        return response()->json(['message' => 'Column preferences saved.']);
    }
}
