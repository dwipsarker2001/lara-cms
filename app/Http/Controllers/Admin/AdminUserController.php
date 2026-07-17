<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $admins = Admin::query()->orderBy('name')->get();

        return view('admin.administrators.index', ['admins' => $admins]);
    }

    public function create(): View
    {
        return view('admin.administrators.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email',
            'password' => 'required|min:8',
            'avatar' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] ??= true;

        Admin::create($data);

        return redirect()->route('admin.administrators.index')->with('success', 'Administrator created.');
    }

    public function edit(Admin $admin): View
    {
        return view('admin.administrators.edit', ['admin' => $admin]);
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,'.$admin->id,
            'avatar' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
            $data['password'] = $request->string('password')->toString();
        }

        $admin->update($data);

        return redirect()->route('admin.administrators.index')->with('success', 'Administrator updated.');
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        if ($admin->id === auth('admin')->id()) {
            return redirect()->route('admin.administrators.index')->with('error', 'You cannot delete yourself.');
        }

        $admin->delete();

        return redirect()->route('admin.administrators.index')->with('success', 'Administrator deleted.');
    }
}
