<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\Marketing\Campaign;
use App\Models\Marketing\Contact;
use App\Models\Marketing\DefaultSetting;
use App\Models\Marketing\Group;
use App\Models\Marketing\Schedule;
use App\Models\Marketing\Stats;
use App\Models\Marketing\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    protected $rem_campaigns;

    // index view
    public function index()
    {
        if (! auth()->id()) {
            return redirect()->route('login');
        }

        $user_level = auth()->user()->role;

        // Get paginated campaigns
        $data = Campaign::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get all campaigns for serial numbers
        $allUserCampaigns = Campaign::where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->pluck('id')
            ->toArray();

        $campaignIdToSerial = [];
        foreach ($allUserCampaigns as $index => $id) {
            $campaignIdToSerial[$id] = $index + 1;
        }

        // Preload all stats for these campaigns
        $campaignIds = $data->pluck('id')->toArray();
        $allStats = Stats::whereIn('camp_id', $campaignIds)->get()->groupBy('camp_id');

        // Preload all groups with their contacts count
        $groups = Group::withCount('contacts')
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('id');

        foreach ($data as $campaign) {
            $campaign->user_serial_number = $campaignIdToSerial[$campaign->id] ?? 1;

            $un_sub = [];
            $not_working = [];

            if (isset($allStats[$campaign->id])) {
                foreach ($allStats[$campaign->id] as $stat) {
                    if (! empty($stat->black_list)) {
                        $un_sub = array_merge($un_sub, explode(',', $stat->black_list));
                    }
                    if (! empty($stat->bounced)) {
                        $not_working = array_merge($not_working, explode(',', $stat->bounced));
                    }
                }
            }

            $campaign->un_sub = count(array_unique(array_filter($un_sub)));
            $campaign->not_working = count(array_unique(array_filter($not_working)));

            $totalRecipients = 0;
            $groupsArray = json_decode($campaign->receiver_emails, true);

            if (! empty($groupsArray)) {
                foreach ($groupsArray as $groupData) {
                    $groupId = $groupData['id'] ?? null;
                    if ($groupId && isset($groups[$groupId])) {
                        $totalRecipients += $groups[$groupId]->contacts_count;
                    }
                }
            }

            $campaign->total_recipients = $totalRecipients;
        }

        // Get or create default settings
        $default_setting = DefaultSetting::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'timezone' => '+00:00',
                'delay_time' => '10',
                'time_format' => '12hours',
                'date_format' => 'dd-mm-yyyy',
                'image_url_hide' => '0',
                'disable_notification' => '1',
                'default_from_name' => null,
                'default_from_email' => null,
                'default_header' => null,
                'default_footer' => null,
                'default_reply_to' => null,
            ]
        );

        $rem_campaigns = $this->rem_campaigns;

        return view('marketing.campaign.index', compact('data', 'default_setting', 'rem_campaigns'));
    }

    public function create()
    {
        $rem_campaigns = $this->rem_campaigns;

        return view('marketing.campaign.create', compact('rem_campaigns'));
    }

    public function store(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->route('login');
        }

        $campaigin = Campaign::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
        ]);

        return redirect()->route('app.campaign.edit', $campaigin->id);
    }

    public function edit(Request $request, $id)
    {
        if (! auth()->id()) {
            return redirect()->route('login');
        }

        // If the campaign is not assigned to current user, throw exception
        $result = Campaign::where('user_id', auth()->id())->where('id', $id)->first();
        if (! $result) {
            return view('marketing.forbidden');
        }

        $groups = Group::where('user_id', auth()->id())->get();
        foreach ($groups as $row) {
            $row['count'] = Contact::where('group_id', $row->id)->get()->count();
        }
        // Else go to edit page
        $campaign = Campaign::where('id', $id)->first();
        $initialGroupList = json_decode($campaign->receiver_emails);
        $mylist = Template::where('user_id', auth()->id())->get();

        return view('marketing.campaign.edit', compact('campaign', 'groups', 'initialGroupList', 'mylist'));
    }

    public function update(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->route('login');
        }

        $edit_campaign = [
            'user_id' => auth()->id(),
            'name' => $request->name,
            'from_email' => $request->from_email,
            'from_name' => $request->from_name,
            'reply_to' => '',
            'name_to' => '',
            'receiver_emails' => $request->receiver_emails,
            'subject_line' => $request->subject_line,
            'preview_text' => $request->preview_text,
            'template_id' => $request->template_id,
            'active_google_analytics' => $request->active_google_analytics == 'on' ? 1 : 0,
            'embed_images' => $request->embed_images == 'on' ? 1 : 0,
            'add_tag' => $request->add_tag == 'on' ? 1 : 0,
            'add_attachment' => $request->add_attachment == 'on' ? 1 : 0,
            'custom_unsubscribe' => $request->custom_unsubscribe == 'on' ? 1 : 0,
            'update_profile_form' => $request->update_profile_form == 'on' ? 1 : 0,
            'enable_mirror' => $request->enable_mirror == 'on' ? 1 : 0,
        ];
        Campaign::where('id', $request->id)->update($edit_campaign);
        $mylist = Template::where('user_id', auth()->id())->get();

        switch ($request->action) {
            case 'campaign':
                return redirect()->route('app.campaign.index')->with('success', 'Your campaign is successfully updated.');
                break;
            case 'template':
                return view('marketing.templates.select', ['campaign_id' => $request->id, 'mylist' => $mylist]);
                break;
        }
    }

    public function usetemplate(Request $request)
    {
        $pos = strpos($request->template_id, 'predefined');

        if ($pos === false) {
            $edit_campaign = [
                'template_id' => $request->template_id,
            ];
        } else {
            $edit_campaign = [
                'template_id' => intval(substr($request->template_id, 10)),
            ];
        }
        Campaign::where('id', $request->campaign_id)->update($edit_campaign);

        return redirect()->route('app.campaign.edit', $request->campaign_id)->with('success', 'The template is successfully selected.');
    }

    public function delete(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->route('login');
        }

        // If the campaign is not assigned to current user, throw exception
        $result = Campaign::where('user_id', auth()->id())->where('id', $request->id)->first();
        if (! $result) {
            return view('marketing.forbidden');
        }

        Campaign::where('id', $request->id)->delete();

        return redirect()->route('app.campaign.index')->with('success', 'It is successfully removed.');
    }

    public function duplicate(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->route('login');
        }

        // If the campaign is not assigned to current user, throw exception
        $result = Campaign::where('user_id', auth()->id())->where('id', $request->id)->first();
        if (! $result) {
            return view('marketing.forbidden');
        }

        $result = Campaign::where('id', $request->id)->first();

        unset($result->id);

        Campaign::create($result->toArray());

        return redirect()->route('app.campaign.index')->with('success', 'It is successfully duplicated.');
    }

    public function sendtest(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->route('login');
        }

        // If the campaign is not assigned to current user, throw exception
        $result = Campaign::where('user_id', auth()->id())->where('id', $request->id)->first();
        if (! $result) {
            return view('marketing.forbidden');
        }

        $result = Campaign::where('id', $request->id)->first();

        if ($result->template_id < 0) {
            $templateId = 'predefined'.$result->template_id;
            $templateId = 'predefined1';
        } else {
            $templateId = $result->template->template_id ?? '';
            if ($result->template == null) {
                return redirect()->route('app.campaign.index')->with('error', "Campaign template doesn't exist.");
            }
        }

        $param = ['stats' => '', 'contact' => ''];
        $content = view('emails.'.$templateId, $param)->render();
        $address = $request->receiver_email;
        $subject = $result->subject_line;
        $from_email = $result->from_email;
        $from_name = $result->from_name;

        $data = [
            'personalizations' => [
                [
                    'to' => [
                        [
                            'email' => $address,
                        ],
                    ],
                    'subject' => $subject,
                ],
            ],
            'content' => [
                [
                    'type' => 'text/html',
                    'value' => $content,
                ],
            ],
            'from' => [
                'email' => 'info@techics.net',
                'name' => $from_name,
            ],
            'reply_to' => [
                'email' => 'info@techics.net',
                'name' => $from_name,
            ],
        ];
        $jsonData = json_encode($data);

        try {
            $apiKey = env('SENDGRID_APIKEY');
            $url = env('SENDGRID_APIENDPOINT');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer '.$apiKey,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                exit('SendGrid cURL request failed: '.curl_error($ch));
            }

            $responseData = json_decode($response, true);

            print_r($responseData);
        } catch (\Exception $e) {
        }

        return redirect()->route('app.campaign.index')->with('success', 'The test email is successfully sent.');
    }

    public function sendcampaign(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->route('login');
        }

        // Check campaign ownership
        $campaign = Campaign::where('user_id', auth()->id())
            ->where('id', $request->id)
            ->first();

        if (! $campaign) {
            return view('marketing.forbidden');
        }

        // Check template exists
        if ($campaign->template_id == null) {
            return redirect()->route('app.campaign.index')->with('error', "Campaign template doesn't exist.");
        }

        // Dispatch coordinator job — it handles everything else
        SendEmailJob::dispatch($request->id, auth()->id());

        // send response
        $success_message = 'The campaign emails have been queued and will be sent shortly.';

        return redirect()->route('app.campaign.index')->with('success', $success_message);
    }

    public function getTotalRecipientsAttribute(): int
    {
        $groups = json_decode($this->receiver_emails, true);

        if (empty($groups) || ! is_array($groups)) {
            return 0;
        }

        $groupIds = collect($groups)->pluck('id')->filter()->toArray();

        if (empty($groupIds)) {
            return 0;
        }

        return Contact::whereIn('group_id', $groupIds)->count();
    }

    public function schedule(Request $request)
    {
        $campId = $request->camp_id;
        $scheduleDate = $request->scheduleDate;
        $scheduleTime = $request->scheduleTime;

        $camp = Campaign::where('id', $campId)->first();

        if (is_null($camp->schedule)) {
            // Create a new schedule
            $schedule = Schedule::create([
                'camp_id' => $campId,
                'schedule_date' => $scheduleDate,
                'schedule_time' => $scheduleTime,
                'status' => 'scheduled',
            ]);
        } else {
            // Update the existing schedule
            $camp->schedule->update([
                'schedule_date' => $scheduleDate,
                'schedule_time' => $scheduleTime,
                'status' => 'scheduled',
            ]);
        }

        // Additional logic if needed
        return redirect()->back()->with('message', 'Schedule updated successfully');
    }

    public function handle(Request $request)
    {
        // Log the incoming webhook data for debugging purposes
        \Log::info('SendGrid Webhook Data:', $request->all());
        \Log::channel('webhook')->info('Email sending event webhook received');
        \Log::channel('webhook')->info('SendGrid Webhook Data:', $request->all());
        // Retrieve the latest stats
        $stats = Stats::orderBy('created_at', 'desc')->first();

        // Initialize the bounced list
        $totalList = $stats->bounced ? explode(',', $stats->bounced) : [];

        // Decode the incoming JSON data
        $events = json_decode($request->getContent(), true);

        foreach ($events as $event) {
            if ((isset($event['event']) && $event['event'] === 'bounce') || (isset($event['event']) && $event['event'] === 'deferred')) {
                // Handle the bounce
                if (! in_array($event['email'], $totalList)) {
                    array_push($totalList, $event['email']);
                }
                $contact = Contact::where('email', $event['email'])->first();
                $contact->exist = 0;
                $contact->save();
                $stats->bounced = implode(',', $totalList);
                $stats->save();

                // Log the bounced email
                \Log::channel('webhook')->info('Bounced email: '.$event['email']);
            } elseif (isset($event['event']) && $event['event'] === 'open') {
                // Handle the open event
                \Log::channel('webhook')->info('Opened email: '.$event['email']);
            }
        }

        return response()->json(['message' => 'Event received']);
    }
}
