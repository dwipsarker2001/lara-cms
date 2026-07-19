<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Campaign;
use App\Models\Marketing\Contact;
use App\Models\Marketing\Group;
use App\Models\Marketing\Stats;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // ======== Group Controller ========== //

    public function groupindex()
    {

        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }
        $data = Group::where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(10);

        foreach ($data as $row) {
            $row['count'] = Contact::where('group_id', $row->id)->count();
        }

        // Total contacts belonging to this user (through groups)
        $totalContacts = Contact::join('groups', function ($join) {
            $join->on('contacts.group_id', '=', 'groups.id')
                ->where('groups.user_id', auth()->id());
        })->count();

        // Total unsubscribers from Stats black_list (same pattern as DashboardController)
        $stats = Stats::where('user_id', auth()->id())->get();
        $totalUnsubscribe = 0;
        foreach ($stats as $val) {
            $totalUnsubscribe += $val->black_list != null
                ? count(explode(',', $val->black_list))
                : 0;
        }

        $rem_groups = 0;

        return view('marketing.groups.index', compact('data', 'rem_groups', 'totalContacts', 'totalUnsubscribe'));
    }

    public function groupcreate()
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        $rem_groups = 0;

        return view('marketing.groups.create', compact('rem_groups'));
    }

    public function groupstore(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        $groupCnt = Group::get()->count();

        $new_group = [
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ];
        Group::create($new_group);

        return redirect()->route('app.group.index')->with('success', 'Your group is successfully created.');
    }

    public function groupedit($id)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        $group = Group::where('id', $id)->first();

        return view('marketing.groups.edit', compact('group'));
    }

    public function groupupdate(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        $edit_group = [
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ];
        Group::where('id', $request->id)->update($edit_group);

        return redirect()->route('app.group.index')->with('success', 'Your group is successfully updated.');
    }

    public function groupdelete(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        Group::where('user_id', auth()->id())->where('id', $request->id)->delete();
        Contact::where('group_id', $request->id)->delete();

        return redirect()->route('app.group.index')->with('success', 'Your group and contact(s) in it are successfully removed.');

    }

    // index view
    public function index($groupId)
    {
        $groups = Group::where('user_id', auth()->id())->where('id', $groupId)->get();
        if (! $groups) {
            return view('marketing.forbidden');
        }

        $data = Contact::where('group_id', $groupId)->orderBy('created_at', 'desc')->paginate(10);

        return view('marketing.contacts.index', compact('data', 'groupId'));
    }

    public function create($groupId)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        return view('marketing.contacts.create', compact('groupId'));
    }

    public function store(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        // Check for duplicate email across all groups for the same user
        $contacts = Contact::where('email', $request->email)
            ->where('id', auth()->id())
            ->get();
        if (count($contacts) != 0) {
            return redirect()->back()->with('error', 'Email is already taken in another group');
        }

        $new_contact = [
            'user_id' => auth()->id(),
            'email' => $request->email,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'sms' => $request->sms,
            'whatsapp' => $request->whatsapp,
            'double_opt_in' => $request->double_opt_in,
            'opt_in' => $request->opt_in,
            'group_id' => $request->groupId,
        ];
        Contact::create($new_contact);
        $groups = Contact::where('group_id', $request->groupId)->get();
        $data = Campaign::where('user_id', auth()->id())->get();
        foreach ($data as $row) {
            // Decode the receiver_emails field
            $temp = json_decode($row->receiver_emails, true);

            foreach ($temp as $key => $item) {
                if ($item['id'] == $request->groupId) {
                    // Update the GROUP field

                    $temp[$key]['label'] = explode('Contacts (', $temp[$key]['label'])[0].'Contacts ('.count($groups).')';
                }
            }
            $data1 = Campaign::where('id', $row->id)->first();
            $data1->receiver_emails = json_encode($temp);
            $data1->save();
        }

        return redirect()->route('app.contact.index', $request->groupId)->with('success', 'Your contact is successfully created.');
    }

    public function edit(Request $request, $id)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        // If the contact is not assigned to current user, throw exception
        $result = Contact::where('id', $id)->first();
        // if(!$result || $result->group->user_id != auth()->id())
        //     return view('marketing.forbidden');

        // Else go to edit page
        $contact = Contact::where('id', $id)->first();

        return view('marketing.contacts.edit', compact('contact'));
    }

    public function update(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        // Check for duplicate email across all groups for the same user (excluding current contact)
        $existingContact = Contact::where('email', $request->email)
            ->where('id', auth()->id())
            ->first();
        if ($existingContact) {
            return redirect()->back()->with('error', 'Email is already taken in another group');
        }

        $edit_contact = [
            'email' => $request->email,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'sms' => $request->sms,
            'whatsapp' => $request->whatsapp,
            'double_opt_in' => $request->double_opt_in,
            'opt_in' => $request->opt_in,
        ];
        Contact::where('id', $request->id)->update($edit_contact);

        return redirect()->route('app.contact.index', $request->group_id)->with('success', 'Your contact is successfully updated.');
    }

    public function delete(Request $request)
    {
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        // If the contact is not assigned to current user, throw exception
        $result = Contact::where('group_id', $request->group_id)->where('id', $request->id)->first();
        if (! $result) {
            return view('marketing.forbidden');
        }

        Contact::where('id', $request->id)->delete();
        $groups = Contact::where('group_id', $request->group_id)->get();
        $data = Campaign::where('user_id', auth()->id())->get();
        foreach ($data as $row) {
            // Decode the receiver_emails field
            $temp = json_decode($row->receiver_emails, true);

            foreach ($temp as $key => $item) {
                if ($item['id'] == $request->group_id) {
                    // Update the GROUP field

                    $temp[$key]['label'] = explode('Contacts (', $temp[$key]['label'])[0].'Contacts ('.count($groups).')';
                }
            }
            $data1 = Campaign::where('id', $row->id)->first();
            $data1->receiver_emails = json_encode($temp);
            $data1->save();
        }

        return redirect()->route('app.contact.index', $request->group_id)->with('success', 'It is successfully removed.');
    }

    public function deleteSelected(Request $request)
    {
        $selected = json_decode($request->selected);
        if (! auth()->id()) {
            return redirect()->to(route('login'));
        }

        $result = Contact::where('group_id', $request->group_id)->whereIn('id', $selected)->first();

        if (! $result || $result->group->user_id != auth()->id()) {
            return view('marketing.forbidden');
        }

        Contact::whereIn('id', $selected)->delete();
        echo json_encode('success');
        // return redirect()->route('app.contact.index')->with('success', 'Selected Contat(s) successfully removed');
    }

    public function import($groupId)
    {
        return view('marketing.contacts.import', compact('groupId'));
    }

    public function fileimport(Request $request, $groupId)
    {
        $type = $request->type;
        $file = $request->file('file');
        if ($file) {
            $filename = time().'_'.$file->getClientOriginalName();
        }

        $extension = $file->getClientOriginalExtension(); // Get extension of uploaded file

        // == Throw exception === //
        // if($extension != 'xls' && $extension != 'xlsx' && $extension != 'txt')
        //     return;

        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize(); // Get size of uploaded file in bytes
        // Check for file extension and size
        // $this->checkUploadedFileProperties($extension, $fileSize);
        // Where uploaded file will be stored on the server
        $location = 'uploads'; // Created an "uploads" folder for that
        // Upload file
        $file->move('public/'.$location, $filename);
        // In case the uploaded file path is to be stored in the database
        $filepath = public_path($location.'/'.$filename);
        // Reading file
        $file = fopen($filepath, 'r');

        $importData_arr = []; // Read through the file and store the contents as an array
        $i = 0;
        // Read the contents of the uploaded file
        while (($filedata = fgetcsv($file, 1000, ',')) !== false) {
            $num = count($filedata);
            // Skip first row (Remove below comment if you want to skip the first row)
            if ($i == 0) {
                if (! ($type == 'hybrid' && $num == 7) && ! ($type == 'google' && $num == 31)) {
                    return redirect()->back()->with('error', 'The imported file is invalid. Please check sample templates regarding to type.');
                }
                $i++;

                continue;
            }
            for ($c = 0; $c < $num; $c++) {
                $importData_arr[$i][] = $filedata[$c];
            }
            $i++;
        }

        return view('marketing.contacts.import-process', ['data' => $importData_arr, 'filename' => $filename, 'type' => $type, 'groupId' => $groupId]);
    }

    public function upload(Request $request, $groupId)
    {
        $filename = $request->filename;
        $type = $request->type;

        $location = 'uploads'; // Created an "uploads" folder for that
        $filepath = public_path($location.'/'.$filename);
        // Reading file
        $file = fopen($filepath, 'r');

        $importData_arr = []; // Read through the file and store the contents as an array
        $i = 0;
        // Read the contents of the uploaded file
        while (($filedata = fgetcsv($file, 1000, ',')) !== false) {
            $num = count($filedata);
            // Skip first row (Remove below comment if you want to skip the first row)
            if ($i == 0) {
                $i++;

                continue;
            }

            if ($filedata[0] != '') {
                if ($type == 'hybrid') {
                    $new_contact = [
                        'group_id' => $groupId,
                        'email' => $filedata[0],
                        'lastname' => isset($filedata[1]) ? $filedata[1] : '',
                        'firstname' => isset($filedata[2]) ? $filedata[2] : '',
                        'sms' => isset($filedata[3]) ? $filedata[3] : '',
                        'whatsapp' => isset($filedata[4]) ? $filedata[4] : '',
                        'double_opt_in' => isset($filedata[5]) ? $filedata[5] : '',
                        'opt_in' => isset($filedata[6]) ? $filedata[6] : '',
                    ];
                } elseif ($type == 'google') {
                    $new_contact = [
                        'group_id' => $groupId,
                        'email' => $filedata[30],
                        'lastname' => isset($filedata[1]) ? $filedata[1] : '',
                        'firstname' => isset($filedata[3]) ? $filedata[3] : '',
                        'sms' => '',
                        'whatsapp' => '',
                        'double_opt_in' => '',
                        'opt_in' => '',
                    ];
                }
                // Check if email already exists in any group for this user
                $existingContact = Contact::where('email', $new_contact['email'])
                    ->where('id', auth()->id())
                    ->first();

                if (! $existingContact) {
                    // Add user_id to the contact data
                    $new_contact['user_id'] = auth()->id();
                    Contact::create($new_contact);
                }
                // $contacts = Contact::where('email', $new_contact['email'])->get();
                // if(count($contacts) != 0)
                //     Contact::create($new_contact);

                // if(count($contacts) != 0)
                //     return redirect()->back()->with('error', 'Email is already taken');
            }

            $i++;
        }

        return redirect()->route('app.contact.index', $groupId)->with('success', 'Your contact is imported successfuly.');
    }

    public function show($groupId, $contactId)
    {
        // Make sure the contact exists and belongs to the group
        $contact = Contact::where('id', $contactId)
            ->where('group_id', $groupId)
            ->first();

        if (! $contact) {
            return view('marketing.forbidden'); // or abort(404)
        }

        return view('marketing.contacts.show', compact('contact', 'groupId'));
    }
}
