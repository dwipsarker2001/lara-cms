<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Campaign;
use App\Models\Marketing\Contact;
use App\Models\Marketing\Profiles;
use App\Models\Marketing\Stats;
use App\Models\Marketing\Template;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'weekly');

        $startDate = match ($filter) {
            'monthly' => now()->subDays(30),
            'weekly' => now()->subDays(7),
            default => now()->subDays(7),
        };

        $contactsNum = Contact::join('groups', function ($join) {
            $join->on('contacts.group_id', '=', 'groups.id')
                ->where('groups.user_id', auth()->id());
        })->get()->count();

        $templatesNum = Template::where('user_id', auth()->id())->count();
        $campaignNum = Campaign::where('user_id', auth()->id())->count();

        $allContactNum = Contact::count();
        $allTemplateNum = Template::count();
        $allCampaignNum = Campaign::count();

        $stats = Stats::where('user_id', auth()->id())
            ->where('created_at', '>=', $startDate)
            ->get();

        $total = 0;
        $opened = 0;
        $clicked = 0;
        $blackList = 0;

        foreach ($stats as $val) {
            $total += $val->total_sent != null ? count(explode(',', $val->total_sent)) : 0;
            $opened += $val->opened != null ? count(explode(',', $val->opened)) : 0;
            $clicked += $val->clicked != null ? count(explode(',', $val->clicked)) : 0;
            $blackList += $val->black_list != null ? count(explode(',', $val->black_list)) : 0;
        }

        $progress = 0;
        $progress += $campaignNum != 0 ? 33 : 0;
        $progress += $contactsNum != 0 ? 33 : 0;
        $progress += $templatesNum != 0 ? 34 : 0;

        $profile = Profiles::firstOrCreate(['user_id' => auth()->id()], ['email' => auth()->user()->email]);
        $package_name = 'Free';
        $can_upgrade = false;
        $rem_emails = 0;
        $rem_campaigns = 0;
        $rem_groups = 0;

        return view('marketing.dashboard', compact(
            'contactsNum', 'templatesNum', 'campaignNum',
            'allContactNum', 'allTemplateNum', 'allCampaignNum',
            'progress', 'total', 'opened', 'clicked', 'blackList',
            'filter', 'profile', 'package_name', 'can_upgrade',
            'rem_emails', 'rem_campaigns', 'rem_groups'
        ));
    }
}
