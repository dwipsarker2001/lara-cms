<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '') {
            return response()->json(['contacts' => [], 'campaigns' => [], 'templates' => []]);
        }

        $term = '%'.$q.'%';

        // Contacts: only those belonging to groups owned by this user
        $contacts = DB::table('contacts')
            ->join('groups', 'contacts.group_id', '=', 'groups.id')
            ->where('groups.user_id', auth()->id())
            ->where(function ($w) use ($q) {
                $w->where('contacts.firstname', 'like', '%'.$q.'%')
                    ->orWhere('contacts.lastname', 'like', '%'.$q.'%')
                    ->orWhere('contacts.email', 'like', '%'.$q.'%');
            })
            ->select('contacts.id', 'contacts.firstname', 'contacts.lastname', 'contacts.email', 'contacts.opt_in', 'contacts.group_id')
            ->limit(10)
            ->get();

        // Campaigns for this user
        $campaigns = DB::table('campaigns')
            ->where('user_id', auth()->id())
            ->where('name', 'like', $term)
            ->select('id', 'name', 'created_at')
            ->limit(6)
            ->get();

        // Templates for this user
        $templates = DB::table('templates')
            ->where('user_id', auth()->id())
            ->where('name', 'like', $term)
            ->select('id', 'name', 'template_id', 'created_at')
            ->limit(6)
            ->get();

        // Prepare response objects in the shape the frontend expects
        $palette = ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#6366f1', '#ef4444', '#0ea5ff'];

        $cdata = [];
        foreach ($contacts as $c) {
            $fname = trim($c->firstname ?? '');
            $lname = trim($c->lastname ?? '');
            $name = trim($fname.' '.$lname);
            if ($name === '') {
                $name = $c->email;
            }

            $initials = '';
            if ($fname !== '' || $lname !== '') {
                $initials = strtoupper(substr(($fname ?: $lname), 0, 1).(isset($lname[0]) ? $lname[0] : ''));
            } else {
                $local = explode('@', $c->email)[0];
                $initials = strtoupper(substr($local, 0, 2));
            }

            $color = $palette[$c->id % count($palette)];

            $cdata[] = [
                'id' => $c->id,
                'group_id' => $c->group_id,
                'name' => $name,
                'email' => $c->email,
                'initials' => $initials,
                'color' => $color,
                'guest' => ($c->opt_in == 0),
                // Link to the contact's group/contact page
                'url' => route('app.contact.show', [$c->group_id, $c->id]),
            ];
        }

        $camdata = [];
        foreach ($campaigns as $cm) {
            $camdata[] = [
                'id' => $cm->id,
                'name' => $cm->name,
                'date' => date('j M Y', strtotime($cm->created_at)),
                'by' => '',
                'bgColor' => '#f8fafc',
                'iconColor' => '#3b82f6',
                'icon' => 'hgi-mail-open',
                // Direct link to edit/view the campaign
                'url' => route('app.campaign.edit', $cm->id),
            ];
        }

        $tdata = [];
        foreach ($templates as $t) {
            // template_id is the folder name used for the designer
            $tid = $t->template_id ?? $t->id;
            $tdata[] = [
                'id' => $t->id,
                'name' => $t->name,
                'date' => date('j M Y', strtotime($t->created_at)),
                'by' => '',
                'bgColor' => '#fff7ed',
                'iconColor' => '#f97316',
                'icon' => 'hgi-file-02',
                // Link to open the template in the designer
                'url' => route('app.template.design', ['id' => $tid, 'type' => 'user']),
            ];
        }

        return response()->json(['contacts' => $cdata, 'campaigns' => $camdata, 'templates' => $tdata]);
    }
}
