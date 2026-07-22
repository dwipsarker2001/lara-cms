<?php

namespace App\Jobs;

use App\Models\Marketing\Campaign;
use App\Models\Marketing\Contact;
use App\Models\Marketing\Stats;
use App\Models\Marketing\Template;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSingleEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 60;

    public $backoff = [30, 60, 120];

    protected $campaignId;

    protected $userId;

    protected $contactId;

    public function __construct($campaignId, $userId, $contactId)
    {
        $this->campaignId = $campaignId;
        $this->userId = $userId;
        $this->contactId = $contactId;
    }

    public function handle()
    {
        $campaign = Campaign::where('user_id', $this->userId)
            ->where('id', $this->campaignId)
            ->first();

        if (! $campaign) {
            return;
        }

        $contact = Contact::find($this->contactId);
        if (! $contact) {
            return;
        }

        // Resolve template
        if ($campaign->template_id < 0) {
            $absId = abs($campaign->template_id);
            $templateView = $absId === 1 ? 'predefined1' : 'predefined-'.$absId;
        } else {
            $template = Template::find($campaign->template_id);
            if (! $template) {
                Log::error('Template not found', ['template_id' => $campaign->template_id]);

                return;
            }
            $templateView = $template->template_id;
        }

        $stats = Stats::firstOrCreate([
            'user_id' => $this->userId,
            'camp_id' => $this->campaignId,
        ]);

        $param = [
            'stats' => $stats->id,
            'contact' => $contact->id,
            'UNSUBSCRIBE_URL' => config('app.url').'/unsubscribe',
        ];

        $viewName = 'emails.'.$templateView;

        if (! view()->exists($viewName)) {
            Log::error('Email view not found', ['view' => $viewName]);
            $this->updateStats($stats, 'bounced', $contact->id);

            return;
        }

        $content = view($viewName, $param)->render();

        Log::info('Rendered email content', [
            'view' => $viewName,
            'content' => $content,
        ]);

        $statusCode = $this->sendEmail(
            $campaign->from_email,
            $campaign->from_name,
            $contact->email,
            $campaign->subject_line,
            $content
        );

        $column = $statusCode == 202 ? 'total_sent' : 'bounced';
        $this->updateStats($stats, $column, $contact->id);

        Log::info('Email sent', [
            'contact_id' => $contact->id,
            'campaign_id' => $this->campaignId,
            'status' => $statusCode,
        ]);
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
            'reply_to' => [
                'email' => 'legalnews@icslegal.com',
                'name' => 'Latest Legal News',
            ],
            'tracking_settings' => [
                'open_tracking' => ['enable' => true],
            ],
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => env('SENDGRID_APIENDPOINT') ?: 'https://api.sendgrid.com/v3/mail/send',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.Setting::getSendGridApiKey(),
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            Log::error('SendGrid cURL Error', ['error' => curl_error($ch)]);
        } elseif ($httpCode !== 202) {
            Log::error('SendGrid API Failed Response', [
                'status' => $httpCode,
                'response' => $response,
                'from_email' => $from_email,
                'to_email' => $to_email,
            ]);
        }

        curl_close($ch);

        return $httpCode;
    }

    protected function updateStats(Stats $stats, string $column, int $contactId): void
    {
        $stats = Stats::lockForUpdate()->find($stats->id);
        $existing = $stats->$column ? explode(',', $stats->$column) : [];

        if (! in_array($contactId, $existing)) {
            $existing[] = $contactId;
            $stats->$column = implode(',', $existing);
            $stats->save();
        }
    }
}
