<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Profiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    // Index view
    public function index()
    {
        $profile = Profiles::where('user_id', auth()->id())->first();

        return view('marketing.account', compact('profile'));
    }

    // Save profile data
    public function save(Request $request)
    {
        Profiles::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'title' => $request->title,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'account_type' => $request->account_type,
                'company_name' => $request->company_name,
                'industry' => $request->industry,
                'employees_number' => $request->employees_number,
                'is_email' => $request->is_email == 'on',
                'is_phone' => $request->is_phone == 'on',
                'is_sms' => $request->is_sms == 'on',
                'is_post' => $request->is_post == 'on',
            ]
        );

        if ($request->new_password != '') {
            if ($request->new_password !== $request->confirm_password) {
                return redirect()->route('app.account.index')->with('error', 'New password and confirm password do not match.');
            } else {
                $hash = Hash::make($request->new_password);
                DB::table('wp_users')
                    ->where('ID', auth()->id())
                    ->update(['user_pass' => $hash]);

                return redirect()->route('app.account.index')->with('success', 'Your account details and new password have been updated successfully.');
            }
        }

        return redirect()->route('app.account.index')->with('success', 'Your account details have been updated successfully.');
    }
}
