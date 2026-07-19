<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Contact;
use App\Models\Marketing\Stats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TrackerController extends Controller
{
    public function clickTracker(Request $request)
    {
        $id = $request->stats;
        $contact_id = $request->contact;
        $url = $request->turl;

        $stats = Stats::findOrFail($id);

        $totalList = $stats->total_sent ? explode(',', $stats->total_sent) : [];
        $clickedList = $stats->clicked ? explode(',', $stats->clicked) : [];

        if (in_array($contact_id, $totalList)) {

            if (! in_array($contact_id, $clickedList)) {
                $clickedList[] = $contact_id;
                $stats->clicked = implode(',', $clickedList);
                $stats->save();
            }
        }

        return Redirect::to($url);
    }

    public function openTracker(Request $request)
    {
        $id = $request->stats;
        $contact_id = $request->contact;

        $stats = Stats::findOrFail($id);

        $totalList = $stats->total_sent != null ? explode(',', $stats->total_sent) : [];
        $openedList = $stats->opened != null ? explode(',', $stats->opened) : [];
        $clickedList = $stats->clicked != null ? explode(',', $stats->opened) : [];
        if (in_array($contact_id, $totalList)) {
            if (count($openedList) == 0) {
                $stats->opened = $contact_id;
                $stats->save();

                return 1;
            } else {
                if (in_array($contact_id, $openedList)) {
                    return 2;
                } else {
                    $stats->opened = $stats->opened.','.$contact_id;
                    $stats->save();

                    return 3;
                }
            }
        } else {
            return 4;
        }

        return 5;
    }

    public function unsubscribeTracker(Request $request)
    {

        $statsId = $request->stats;
        $contactId = $request->contact;

        $stats = Stats::findOrFail($statsId);
        $contact = Contact::find($contactId);

        // If the contact exists and was part of this campaign
        $totalSentList = $stats->total_sent ? explode(',', $stats->total_sent) : [];

        if ($contact && in_array($contactId, $totalSentList)) {

            // Update the contact globally
            if (! $contact->is_unsubscribed) {
                $contact->update([
                    'is_unsubscribed' => true,
                    'unsubscribed_at' => now(),
                ]);
            }

            // Update Stats table to track this campaign unsubscribe
            // Use an array instead of CSV if possible, but keeping CSV for backward compatibility
            $blackList = $stats->black_list ? explode(',', $stats->black_list) : [];

            if (! in_array($contactId, $blackList)) {
                $blackList[] = $contactId;
                $stats->black_list = implode(',', $blackList);
                $stats->save();
            }

            return view('marketing.unsubscribe');

        } else {
            return view('marketing.unsubscribe');
        }
    }
}
