<?php

namespace App\Jobs;

use App\Models\Marketing\Campaign;
use App\Models\Marketing\Contact;
use App\Models\Marketing\Stats;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 3600;

    protected $campaignId;

    protected $userId;

    public function __construct($campaignId, $userId)
    {
        $this->campaignId = $campaignId;
        $this->userId = $userId;
    }

    public function handle()
    {
        // Get campaign
        $campaign = Campaign::where('user_id', $this->userId)
            ->where('id', $this->campaignId)
            ->first();

        if (! $campaign) {
            Log::error('Campaign not found', [
                'campaign_id' => $this->campaignId,
                'user_id' => $this->userId,
            ]);

            return;
        }

        // Build alreadySent hash map from stats.total_sent CSV
        // Using isset() = O(1) lookup instead of in_array() = O(N)
        $alreadySentIds = [];

        Stats::where('user_id', $this->userId)
            ->where('camp_id', $this->campaignId)
            ->pluck('total_sent')
            ->each(function ($csv) use (&$alreadySentIds) {
                if (! empty($csv)) {
                    foreach (explode(',', $csv) as $id) {
                        $id = trim($id);
                        if ($id !== '') {
                            $alreadySentIds[$id] = true;
                        }
                    }
                }
            });

        // Get contact group IDs from campaign
        $receiver_emails = json_decode($campaign->receiver_emails);

        if (empty($receiver_emails)) {
            Log::error('No receiver groups found', [
                'campaign_id' => $this->campaignId,
            ]);

            return;
        }

        $group_list = array_map(fn ($g) => $g->id, $receiver_emails);

        $dispatched = 0;

        // Fetch contacts:
        // - Filter by group
        // - Skip globally unsubscribed contacts at DB level (indexed, O(1))
        // - Chunk to handle 50K+ contacts without memory issues
        Contact::whereIn('group_id', $group_list)
            ->where('is_unsubscribed', false)
            ->chunkById(500, function ($contacts) use ($alreadySentIds, &$dispatched) {
                foreach ($contacts as $contact) {

                    // Skip if already sent in this campaign
                    if (isset($alreadySentIds[$contact->id])) {
                        Log::info('Skipping already sent', [
                            'contact_id' => $contact->id,
                            'campaign_id' => $this->campaignId,
                        ]);

                        continue;
                    }

                    // Dispatch individual send job
                    SendSingleEmailJob::dispatch(
                        $this->campaignId,
                        $this->userId,
                        $contact->id
                    );

                    $dispatched++;
                }
            });

        Log::info('SendEmailJob finished', [
            'campaign_id' => $this->campaignId,
            'user_id' => $this->userId,
            'total_dispatched' => $dispatched,
        ]);
    }
}
