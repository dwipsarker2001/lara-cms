<?php

namespace App\Console\Commands;

use App\Models\Marketing\Campaign;
use App\Models\Marketing\Contact;
use App\Models\Marketing\Stats;
use App\Models\Marketing\Template;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScheduleCampaign extends Command
{
    protected $signature = 'schedule:campaign';

    protected $description = 'Send campaign according to the schedule.';

    public function handle()
    {
        $camps = Campaign::whereHas('schedule', function ($query) {
            $query->whereNotNull('schedule_date')
                ->whereNotNull('schedule_time')
                ->where('status', '!=', 'sent');
        })->get();

        foreach ($camps as $camp) {
            $datetime = $camp->schedule->schedule_date.' '.$camp->schedule->schedule_time;
            $carbonDatetime = Carbon::parse($datetime);
            $currentDatetime = Carbon::now();
            $minuteDifference = $carbonDatetime->diffInMinutes($currentDatetime);

            if ($minuteDifference == 0 && $camp->schedule->status != 'sent') {
                $this->sendCampaign($camp);
                $camp->schedule()->update(['status' => 'sent']);
            }
        }
    }

    public function sendCampaign($camp)
    {
        $userId = $camp->user_id;

        $statList = Stats::where('user_id', $userId)->where('camp_id', $camp->id)->get();

        $blackList = [];
        foreach ($statList as $item) {
            if ($item->black_list != '') {
                $new_array = explode(',', $item->black_list);
                $blackList = $blackList + $new_array;
            }
        }

        $stats = Stats::create(['user_id' => $userId, 'camp_id' => $camp->id]);

        if ($camp->template_id < 0) {
            $absId = abs($camp->template_id);
            $templateView = $absId === 1 ? 'predefined1' : 'predefined-'.$absId;
        } else {
            $template = Template::find($camp->template_id);
            if (! $template) {
                Log::error("Campaign template doesn't exist.", ['campaign_id' => $camp->id]);

                return;
            }
            $templateView = $template->template_id;
        }

        $receiver_emails = json_decode($camp->receiver_emails);
        $group_list = [];
        foreach ($receiver_emails as $group) {
            $group_list[] = $group->id;
        }

        $contacts = Contact::whereIn('group_id', $group_list)->get();

        if (count($contacts) == 0) {
            Log::warning('No contacts found for campaign', ['campaign_id' => $camp->id]);

            return;
        }

        try {
            $from_email = $camp->from_email;
            $from_name = $camp->from_name;
            $subject = $camp->subject_line;

            foreach ($contacts as $contact) {
                if (in_array($contact->id, $blackList)) {
                    continue;
                }

                $param = [
                    'stats' => $stats->id,
                    'contact' => $contact->id,
                    'UNSUBSCRIBE_URL' => config('app.url').'/unsubscribe',
                ];

                $viewName = 'emails.'.$templateView;

                if (! view()->exists($viewName)) {
                    Log::error('Email view not found', ['view' => $viewName]);
                    if ($stats->bounced == '') {
                        $stats->bounced = $contact->id;
                    } else {
                        $stats->bounced = $stats->bounced.','.$contact->id;
                    }
                    $stats->save();

                    continue;
                }

                $content = view($viewName, $param)->render();

                $statusCode = $this->sendEmail($from_email, $from_name, $contact->email, $subject, $content);

                if ($statusCode == 202) {
                    if ($stats->total_sent == '') {
                        $stats->total_sent = $contact->id;
                    } else {
                        $stats->total_sent = $stats->total_sent.','.$contact->id;
                    }
                } else {
                    if ($stats->bounced == '') {
                        $stats->bounced = $contact->id;
                    } else {
                        $stats->bounced = $stats->bounced.','.$contact->id;
                    }
                }
                $stats->save();
            }
        } catch (\Exception $e) {
            Log::error('ScheduleCampaign error', [
                'campaign_id' => $camp->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function sendEmail($from_email, $from_name, $to_email, $subject, $content)
    {
        $data = [
            'personalizations' => [[
                'to' => [['email' => $to_email]],
                'subject' => $subject,
            ]],
            'content' => [[
                'type' => 'text/html',
                'value' => $content,
            ]],
            'from' => [
                'email' => $from_email,
                'name' => $from_name,
            ],
            'tracking_settings' => [
                'open_tracking' => ['enable' => true],
            ],
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => env('SENDGRID_APIENDPOINT'),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.env('SENDGRID_APIKEY'),
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            Log::error('SendGrid cURL Error', ['error' => curl_error($ch)]);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode;
    }
}
