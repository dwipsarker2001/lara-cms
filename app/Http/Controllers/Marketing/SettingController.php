<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\DefaultSetting;
use App\Models\Marketing\Sender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    protected $world_timezone;

    public function __construct()
    {
        $this->world_timezone = [
            ['id' => 1, 'value' => '-12:00', 'name' => '(GMT -12:00) Eniwetok Kwajalein'],
            ['id' => 2, 'value' => '-11:00', 'name' => '(GMT -11:00) Midway Island, Samoa'],
            ['id' => 3, 'value' => '-10:00', 'name' => '(GMT -10:00) Hawaii'],
            ['id' => 4, 'value' => '-09:50', 'name' => '(GMT -9:30) Taiohae'],
            ['id' => 5, 'value' => '-09:00', 'name' => '(GMT -9:00) Alaska'],
            ['id' => 6, 'value' => '-08:00', 'name' => '(GMT -8:00) Pacific Time [US &amp; Canada)'],
            ['id' => 7, 'value' => '-07:00', 'name' => '(GMT -7:00) Mountain Time [US &amp; Canada)'],
            ['id' => 8, 'value' => '-06:00', 'name' => '(GMT -6:00) Central Time [US &amp; Canada], Mexico City'],
            ['id' => 9, 'value' => '-05:00', 'name' => '(GMT -5:00) Eastern Time [US &amp; Canada], Bogota, Lima'],
            ['id' => 10, 'value' => '-04:50', 'name' => '(GMT -4:30) Caracas'],
            ['id' => 11, 'value' => '-04:00', 'name' => '(GMT -4:00) Atlantic Time [Canada], Caracas, La Paz'],
            ['id' => 12, 'value' => '-03:50', 'name' => '(GMT -3:30) Newfoundland'],
            ['id' => 13, 'value' => '-03:00', 'name' => '(GMT -3:00) Brazil, Buenos Aires, Georgetown'],
            ['id' => 14, 'value' => '-02:00', 'name' => '(GMT -2:00) Mid-Atlantic'],
            ['id' => 15, 'value' => '-01:00', 'name' => '(GMT -1:00) Azores, Cape Verde Islands'],
            ['id' => 16, 'value' => '+00:00', 'name' => '(GMT) Western Europe Time, London, Lisbon, Casablanca'],
            ['id' => 17, 'value' => '+01:00', 'name' => '(GMT +1:00) Brussels, Copenhagen, Madrid, Paris'],
            ['id' => 18, 'value' => '+02:00', 'name' => '(GMT +2:00) Kaliningrad, South Africa'],
            ['id' => 19, 'value' => '+03:00', 'name' => '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg'],
            ['id' => 20, 'value' => '+03:50', 'name' => '(GMT +3:30) Tehran'],
            ['id' => 21, 'value' => '+04:00', 'name' => '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi'],
            ['id' => 22, 'value' => '+04:50', 'name' => '(GMT +4:30) Kabul'],
            ['id' => 23, 'value' => '+05:00', 'name' => '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent'],
            ['id' => 24, 'value' => '+05:50', 'name' => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi'],
            ['id' => 25, 'value' => '+05:75', 'name' => '(GMT +5:45) Kathmandu, Pokhar'],
            ['id' => 26, 'value' => '+06:00', 'name' => '(GMT +6:00) Almaty, Dhaka, Colombo'],
            ['id' => 27, 'value' => '+06:50', 'name' => '(GMT +6:30) Yangon, Mandalay'],
            ['id' => 28, 'value' => '+07:00', 'name' => '(GMT +7:00) Bangkok, Hanoi, Jakarta'],
            ['id' => 29, 'value' => '+08:00', 'name' => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong'],
            ['id' => 30, 'value' => '+08:75', 'name' => '(GMT +8:45) Eucla'],
            ['id' => 31, 'value' => '+09:00', 'name' => '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk'],
            ['id' => 32, 'value' => '+09:50', 'name' => '(GMT +9:30) Adelaide, Darwin'],
            ['id' => 33, 'value' => '+10:00', 'name' => '(GMT +10:00) Eastern Australia, Guam, Vladivostok'],
            ['id' => 34, 'value' => '+10:50', 'name' => '(GMT +10:30) Lord Howe Island'],
            ['id' => 35, 'value' => '+11:00', 'name' => '(GMT +11:00) Magadan, Solomon Islands, New Caledonia'],
            ['id' => 36, 'value' => '+11:50', 'name' => '(GMT +11:30) Norfolk Island'],
            ['id' => 37, 'value' => '+12:00', 'name' => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka'],
            ['id' => 38, 'value' => '+12:75', 'name' => '(GMT +12:45) Chatham Islands'],
            ['id' => 39, 'value' => '+13:00', 'name' => '(GMT +13:00) Apia, Nukualofa'],
            ['id' => 40, 'value' => '+14:00', 'name' => '(GMT +14:00) Line Islands, Tokelau'],
        ];
    }

    // index view
    public function index(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        return view('marketing.settings.index');
    }

    public function default()
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        $default_setting = DefaultSetting::where('user_id', auth()->id())->first();

        return view('marketing.settings.default', ['default_setting' => $default_setting, 'world_timezone' => $this->world_timezone]);
    }

    public function default_save(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        DefaultSetting::updateOrCreate(
            ['user_id' => auth()->id()],
            ['user_id' => auth()->id(),
                'timezone' => $request->timezone,
                'delay_time' => $request->delay_time,
                'time_format' => $request->time_format,
                'date_format' => $request->date_format,
                'image_url_hide' => $request->image_url_hide,
                'disable_notification' => $request->disable_notification,
                'default_from_name' => $request->default_from_name,
                'default_from_email' => $request->default_from_email,
                'default_header' => $request->default_header,
                'default_footer' => $request->default_footer,
                'default_reply_to' => $request->default_reply_to]
        );

        return redirect()->back()->with('success', 'Your default setting is saved successfully');
    }

    public function sender()
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        $apiKey = \App\Models\Setting::getSendGridApiKey();
        $sg = new \SendGrid($apiKey);

        $senders = Sender::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($senders as $sender) {
            $response = $sg->client->whitelabel()->domains()->_($sender->sendgrid_id)->get();
            $result = json_decode($response->body());

            $row = [
                'sendgrid_id' => $sender->sendgrid_id,
                'domain' => $result->domain,
                'valid' => $result->valid,
                'dns' => json_encode($result->dns),
            ];
            array_push($data, $row);
        }

        return view('marketing.settings.sender', compact('data'));
    }

    public function sender_save(Request $request)
    {
        if (auth()->user()->role == 'user') {
            return;
        }

        $limitation = auth()->user()->role == 'admin' ? 3 : 5;
        // Count only senders belonging to the current user
        $userSenderCount = Sender::where('user_id', auth()->id())->count();

        if ($userSenderCount >= $limitation) {
            Log::info('Sender limit exceeded', [
                'user_sender_count' => $userSenderCount,
                'limitation' => $limitation,
            ]);

            return redirect()->route('app.setting.sender')
                ->with('error', 'You have reached the maximum number of senders allowed for your account level.');
        }

        $domain_name = $request->domain;
        $apiKey = \App\Models\Setting::getSendGridApiKey();
        $sg = new \SendGrid($apiKey);

        $request_body = (object) [
            'domain' => $domain_name,
            'subdomain' => '',
            'ips' => [],
            'custom_spf' => false,
            'default' => false,
            'automatic_security' => true,
            'region' => 'global',
        ];

        try {
            $response = $sg->client->whitelabel()->domains()->post($request_body);
            $result = json_decode($response->body());

            $sendgrid_id = $result->id;
            Sender::create([
                'user_id' => auth()->id(),
                'domain' => $result->domain,
                'sendgrid_id' => $sendgrid_id,
            ]);

            return redirect()->route('app.setting.sender')
                ->with('success', 'Your domain name '.$result->domain.' connection has been requested successfully. Please update the DNS records under your domain name with the values below. For more info click on More Info button.');

        } catch (Exception $ex) {
            return redirect()->route('app.setting.sender')
                ->with('error', 'Error creating domain: '.$ex->getMessage());
        }
    }

    public function sender_edit(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        if (auth()->user()->role == 'user') {
            return redirect()->route('app.setting.sender')->with('error', 'Access denied.');
        }

        $sendgrid_id = $request->sendgrid_id;
        $new_domain = $request->domain;

        // Verify the sender belongs to the current user
        $sender = Sender::where('user_id', auth()->id())
            ->where('sendgrid_id', $sendgrid_id)
            ->first();

        if (! $sender) {
            return redirect()->route('app.setting.sender')
                ->with('error', 'Domain not found or access denied.');
        }

        $apiKey = \App\Models\Setting::getSendGridApiKey();
        $sg = new \SendGrid($apiKey);

        $request_body = (object) [
            'domain' => $new_domain,
            'subdomain' => '',
            'ips' => [],
            'custom_spf' => false,
            'default' => false,
            'automatic_security' => true,
            'region' => 'global',
        ];

        try {
            // Update the domain in SendGrid
            $response = $sg->client->whitelabel()->domains()->_($sendgrid_id)->patch($request_body);
            $result = json_decode($response->body());
            // Update the domain in our database
            Log::info('Result', [
                'result' => $result,
            ]);
            $sender->update([
                'domain' => $new_domain,
            ]);

            return redirect()->route('app.setting.sender')
                ->with('success', 'Domain updated successfully. Please update the DNS records with the new values below.');

        } catch (Exception $ex) {
            return redirect()->route('app.setting.sender')
                ->with('error', 'Error updating domain: '.$ex->getMessage());
        }
    }

    public function sender_delete(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        if (auth()->user()->role == 'user') {
            return redirect()->route('app.setting.sender')->with('error', 'Access denied.');
        }

        $sendgrid_id = $request->sendgrid_id;

        // Verify the sender belongs to the current user
        $sender = Sender::where('user_id', auth()->id())
            ->where('sendgrid_id', $sendgrid_id)
            ->first();

        if (! $sender) {
            return redirect()->route('app.setting.sender')
                ->with('error', 'Domain not found or access denied.');
        }
        Log::info('Sender', [
            'sender' => $sender,
        ]);
        $apiKey = \App\Models\Setting::getSendGridApiKey();
        $sg = new \SendGrid($apiKey);

        try {
            // Delete the domain from SendGrid
            $response = $sg->client->whitelabel()->domains()->_($sendgrid_id)->delete();
            Log::info('Response', [
                'response' => $response,
            ]);
            // Delete from our database
            $sender->delete();

            return redirect()->route('app.setting.sender')
                ->with('success', 'Domain deleted successfully.');

        } catch (Exception $ex) {
            return redirect()->route('app.setting.sender')
                ->with('error', 'Error deleting domain: '.$ex->getMessage());
        }
    }
}
