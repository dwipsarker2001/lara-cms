<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Template;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = Template::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();

        return view('admin.email-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.email-templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'published' => 'boolean',
        ]);

        $data['user_id'] = auth()->id();
        $data['published'] = $request->boolean('published');
        $data['template_id'] = $this->generateRandomString();

        Template::create($data);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template created.');
    }

    public function edit(Template $emailTemplate)
    {
        return view('admin.email-templates.edit', ['template' => $emailTemplate]);
    }

    public function update(Request $request, Template $emailTemplate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'published' => 'boolean',
        ]);

        $emailTemplate->update([
            'name' => $data['name'],
            'published' => $request->boolean('published'),
        ]);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template updated.');
    }

    public function editor(Template $emailTemplate)
    {
        return view('admin.email-templates.editor', ['template' => $emailTemplate]);
    }

    public function saveContent(Request $request, Template $emailTemplate)
    {
        $data = $request->validate([
            'content' => 'required|json',
        ]);

        $emailTemplate->update(['content' => $data['content']]);

        return response()->json(['saved' => true]);
    }

    public function destroy(Template $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template deleted.');
    }

    private function generateRandomString(int $length = 13): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}
