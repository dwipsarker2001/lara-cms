<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\ReportPreference;
use App\Models\Marketing\Stats;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // index view
    public function index()
    {
        $from = date('Y-m-01');
        $to = date('Y-m-d');

        $stats = Stats::where('user_id', auth()->id())
            ->whereHas('campaign', function (Builder $query) {
                $query->whereNotNull('id'); // Ensures the campaign exists
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        $allSent = 0;
        $allRecipient = 0;
        $allOpened = 0;
        $allClicked = 0;
        $allUnsubscribed = 0;
        $allBounced = 0;

        foreach ($stats as $stat) {
            $allSent += $stat->total_sent != null ? count(explode(',', $stat->total_sent)) : 0;
            $allOpened += $stat->opened != null ? count(explode(',', $stat->opened)) : 0;
            $allClicked += $stat->clicked != null ? count(explode(',', $stat->clicked)) : 0;
            $allBounced += $stat->bounced != null ? count(explode(',', $stat->bounced)) : 0;
            $allUnsubscribed += $stat->black_list != null ? count(explode(',', $stat->black_list)) : 0;
        }

        $allRecipient = $allSent - $allBounced;

        $user = User::where('ID', auth()->id())->first();

        return view('marketing.reports.index', compact('user', 'stats', 'allSent', 'allRecipient', 'allOpened', 'allClicked', 'allBounced', 'allUnsubscribed', 'from', 'to'));
    }

    public function filterReportByDate(Request $request)
    {
        $from = date($request->from);
        $to = date($request->to);
        $temp = Carbon::parse($request->to)->addDays(1)->toDateString();

        $stats = Stats::where('user_id', auth()->id())
            ->whereHas('campaign', function (Builder $query) {
                $query->whereNotNull('id'); // Ensures the campaign exists
            })
            ->whereBetween('created_at', [$from, $temp])
            ->get();

        $allSent = 0;
        $allRecipient = 0;
        $allOpened = 0;
        $allClicked = 0;
        $allUnsubscribed = 0;
        $allBounced = 0;

        foreach ($stats as $stat) {
            $allSent += $stat->total_sent != null ? count(explode(',', $stat->total_sent)) : 0;
            $allOpened += $stat->opened != null ? count(explode(',', $stat->opened)) : 0;
            $allClicked += $stat->clicked != null ? count(explode(',', $stat->clicked)) : 0;
            $allBounced += $stat->bounced != null ? count(explode(',', $stat->bounced)) : 0;
            $allUnsubscribed += $stat->black_list != null ? count(explode(',', $stat->black_list)) : 0;
        }

        $allRecipient = $allSent - $allBounced;
        $user = User::where('ID', auth()->id())->first();

        return view('marketing.reports.index', compact('user', 'stats', 'allSent', 'allRecipient', 'allOpened', 'allClicked', 'allBounced', 'allUnsubscribed', 'from', 'to'));
    }

    public function delete($id)
    {
        $stat = Stats::findOrFail($id);
        $stat->delete();

        return redirect()->back();
    }

    public function activeAutomatedWeeklyReport(Request $request)
    {
        $enable = $request->report_enable;
        $preference = ReportPreference::where('user_id', auth()->id());

        // Attempt to update existing preference or create a new one
        ReportPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            ['report_enabled' => $enable]
        );

        return response()->json(['success' => true]);
    }
}
