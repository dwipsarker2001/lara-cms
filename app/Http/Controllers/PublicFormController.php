<?php

namespace App\Http\Controllers;

use App\Models\CollectionEntry;
use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Http\Request;

class PublicFormController extends Controller
{
    public function submit(Request $request, ?Form $form = null)
    {
        if (! $form || ! $form->exists) {
            $form = Form::firstOrCreate(
                ['id' => 1],
                [
                    'title' => 'Traveler Booking Form',
                    'submit_text' => 'Confirm Booking',
                    'success_message' => 'Thank you! Your travel booking has been received.',
                ]
            );
        }

        $formId = $form->id;
        $successMsg = $form->success_message ?? 'Thank you! Your submission has been received.';

        $submittedKeys = array_keys($request->except(['_token', '_method', 'package_id']));

        if (! empty($form->fields)) {
            $rules = [];
            $customMessages = [];

            foreach ($form->fields as $field) {
                if (! is_array($field) || empty($field['name'])) {
                    continue;
                }

                $fieldName = $field['name'];
                $fieldRules = [];

                if (! empty($field['required'])) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                if (! empty($field['validation'])) {
                    $customRules = array_filter(explode('|', $field['validation']));
                    foreach ($customRules as $cr) {
                        $fieldRules[] = trim($cr);
                    }
                }

                if (! empty($fieldRules)) {
                    $rules[$fieldName] = implode('|', array_unique($fieldRules));
                }

                if (! empty($field['error_message'])) {
                    $customMessages["{$fieldName}.*"] = $field['error_message'];
                    $customMessages[$fieldName] = $field['error_message'];
                }
            }

            if (! empty($rules)) {
                $request->validate($rules, $customMessages);
            }
        } else {
            // No schema — use default checkout form validation
            $request->validate([
                'full_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'travel_date' => 'nullable|string',
                'preferred_time' => 'nullable|string',
                'adults' => 'nullable',
                'children' => 'nullable',
                'additional_message' => 'nullable|string',
            ]);
        }

        $data = $request->except(['_token', '_method']);

        FormEntry::create([
            'form_id' => $formId,
            'data' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
            ]);
        }

        // Redirect to the package page if a package_id was submitted
        $packageId = $request->input('package_id');
        if ($packageId) {
            $packageEntry = CollectionEntry::find($packageId);
            if ($packageEntry && $packageEntry->slug) {
                return redirect('/'.$packageEntry->slug)->with('booking_success', $successMsg);
            }
        }

        return back()->with('success', $successMsg);
    }
}
