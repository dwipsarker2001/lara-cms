<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('created_at', 'desc')->get();
        $defaultPlanId = Setting::value('default_subscription_plan_id');
        return view('admin.subscription-plans.index', compact('plans', 'defaultPlanId'));
    }

    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_emails' => 'required|integer|min:0',
            'max_contacts' => 'required|integer|min:0',
            'max_campaigns' => 'required|integer|min:0',
            'max_groups' => 'required|integer|min:0',
        ]);

        $plan = SubscriptionPlan::create($data);

        if ($request->boolean('set_as_default')) {
            Setting::firstOrCreate(['id' => 1])->update(['default_subscription_plan_id' => $plan->id]);
        }

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Plan created successfully.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plans.edit', [
            'plan' => $subscriptionPlan,
            'defaultPlanId' => Setting::value('default_subscription_plan_id'),
        ]);
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_emails' => 'required|integer|min:0',
            'max_contacts' => 'required|integer|min:0',
            'max_campaigns' => 'required|integer|min:0',
            'max_groups' => 'required|integer|min:0',
        ]);

        $subscriptionPlan->update($data);

        if ($request->boolean('set_as_default')) {
            Setting::firstOrCreate(['id' => 1])->update(['default_subscription_plan_id' => $subscriptionPlan->id]);
        } elseif (Setting::value('default_subscription_plan_id') == $subscriptionPlan->id) {
            Setting::where('id', 1)->update(['default_subscription_plan_id' => null]);
        }

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Plan deleted successfully.');
    }
}
