<?php

namespace App\Console\Commands;

use App\Mail\WeeklyReportMail;
use App\Models\Marketing\Stats;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReports extends Command
{
    protected $signature = 'send:reports';

    protected $description = 'Send weekly reports to users who have enabled report preferences.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::with('reportPreference')->whereHas('reportPreference', function ($query) {
            $query->where('report_enabled', true);
        })->get();

        foreach ($users as $user) {
            if ($this->shouldSendReport($user)) {
                if ($user->reportPreference) {
                    $this->sendReport($user);

                    $user->reportPreference->update(['last_report_sent_at' => now()]);
                } else {
                    $this->info("No report preferences found for user {$user->email}");
                }
            }
        }

        $this->info('Weekly reports sent successfully.');
    }

    protected function shouldSendReport(User $user)
    {
        return $user->reportPreference->last_report_sent_at === null ||
            now()->diffInWeeks($user->reportPreference->last_report_sent_at) >= 1;
    }

    protected function sendReport(User $user)
    {
        $stats = Stats::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();

        Mail::to($user->email)->send(new WeeklyReportMail($stats));
        $this->info("Report sent to {$user->email}");
    }
}
