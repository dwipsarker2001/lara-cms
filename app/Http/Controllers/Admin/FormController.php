<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
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
            'description' => 'nullable|string',
            'submit_text' => 'required|string|max:255',
            'success_message' => 'required|string|max:255',
        ]);
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
            'description' => 'nullable|string',
            'submit_text' => 'required|string|max:255',
            'success_message' => 'required|string|max:255',
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
        $entries = Schema::hasTable('form_entries')
            ? $form->entries()->latest()->paginate(15)
            : new LengthAwarePaginator([], 0, 15);

        return view('admin.forms.entries', compact('form', 'entries'));
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

        $fields = collect($form->fields ?? [])->pluck('label', 'name');

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
}
