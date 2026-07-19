<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Form;
use File;
use Illuminate\Http\Request;

class FormController extends Controller
{
    // index view
    public function index()
    {
        $data = Form::where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(env('itemsperpage', 10));

        return view('marketing.forms.index', compact('data'));
    }

    public function create()
    {
        return view('marketing.forms.create');
    }

    public function edit(Request $request, $id)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        $result = Form::where('user_id', auth()->id())->where('id', $id)->first();
        if (! $result) {
            return view('marketing.forbidden');
        }

        $data = Form::where('id', $id)->first();

        return view('marketing.forms.edit', compact('data'));
    }

    public function save(Request $request)
    {
        Form::create([
            'user_id' => auth()->id(),
            'path' => $request->filename,
            'name' => $request->name,
        ]);
    }

    public function update(Request $request)
    {
        Form::where('path', $request->filename)
            ->update([
                'name' => $request->name,
            ]);
    }

    public function delete(Request $request)
    {
        $file = __DIR__.DIRECTORY_SEPARATOR.'../../../public/builders/'.$request->path.'.json';
        Form::where('id', $request->id)->delete();
        File::delete($file);

        $formFile = __DIR__.DIRECTORY_SEPARATOR.'../../../public/forms/form_'.$request->path.'.php';
        File::delete($formFile);

        return redirect()->route('app.form.index')->with('success', 'The form is successfully removed');
    }

    public function submit(Request $request)
    {
        var_dump($request->all());
        exit(0);
    }
}
