<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Template;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TemplateController extends Controller
{
    // Template list view
    public function index()
    {
        $mylist = Template::where('user_id', auth()->id())->get();

        return view('marketing.templates.index', compact('mylist'));
    }

    public function select(Request $request)
    {
        $templateID = $request->id;
        $type = $request->type;
        $name = $request->name;
        $newTemplateID = $this->generateRandomString();

        // Copy Template directory to "user" with new name
        $org_path = public_path('templates/').$type.'/'.$templateID;
        $dist_path = public_path('templates/').'user'.'/'.$newTemplateID;

        File::copyDirectory($org_path, $dist_path); // Copy Template Directory

        return redirect()->route('app.template.design', ['id' => $newTemplateID, 'type' => 'user']);
    }

    public function remove(Request $request)
    {
        $template_id = $request->template_id;
        $path = public_path('templates/').'user'.'/'.$template_id;
        File::deleteDirectory($path);
        Template::where('template_id', $template_id)->delete();
    }

    public function create()
    {
        $newTemplateID = $this->generateRandomString();
        $org_path = public_path('templates/blank');
        $dist_path = public_path('templates/user/'.$newTemplateID);

        File::copyDirectory($org_path, $dist_path);

        return redirect()->route('app.template.design', ['id' => $newTemplateID, 'type' => 'user']);
    }

    public function createPage()
    {
        return view('marketing.templates.create-page');
    }

    public function design(Request $request)
    {
        $type = $request->type;
        $id = $request->id;

        return response()
            ->view('marketing.design.index', compact('id', 'type'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function generateRandomString($length = 13)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function save_name(Request $request)
    {
        $result = Template::where('template_id', $request->template_id)->get();
        if (count($result) == 0) {
            $action_type = 'new';
            $org_name = '';
        } else {
            $action_type = 'edit';
            $org_name = $result[0]['name'];
        }

        return view('marketing.design.save-name', ['template_id' => $request->template_id, 'action_type' => $action_type, 'org_name' => $org_name]);
    }

    public function storeTemplateDB(Request $request)
    {
        $action = $request->action;
        $name = $request->name;
        if ($action == 'save') {
            if ($name == '') {
                return redirect()->back()->with('error', 'Please input template name.');
            }

            if ($request->action_type == 'new') {
                Template::create([
                    'user_id' => auth()->id(),
                    'template_id' => $request->template_id,
                    'name' => $name,
                ]);
            } else {
                Template::where('template_id', $request->template_id)->update([
                    'name' => $name,
                ]);
            }
        } elseif ($action == 'close') {
            $path = public_path('templates/').'user'.'/'.$request->template_id;
            File::deleteDirectory($path);
        }

        return redirect()->route('app.template.index');
    }

    public function save(Request $request)
    {

        header('Content-Type: application/json');
        $templateID = $request->template_id;

        $dist_path = public_path('templates/user/').$templateID;

        $opnUrl = route('openTracker').'?&stats={{$stats}}&contact={{$contact}}';
        $clkUrl = route('clickTracker').'?&stats={{$stats}}&contact={{$contact}}';
        $unsubUrl = route('unsubscribeTracker').'?&stats={{$stats}}&contact={{$contact}}';

        // Decode HTML entities so &amp; becomes & and regex/str_replace work correctly
        $content = html_entity_decode($request->content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // STEP 1: Strip previously injected tracking to avoid duplication
        $content = preg_replace('/<img[^>]*id=["\']confirmImg["\'][^>]*>/i', '', $content);
        $content = preg_replace(
            '/target="_parent"\s+href="[^"]*?&turl=(https?:\/\/[^"]+)"/i',
            'href="$1"',
            $content
        );
        $content = preg_replace(
            '/target="_parent"\s+href="[^"]*?&turl=(www\.[^"]+)"/i',
            'href="$1"',
            $content
        );
        $content = preg_replace(
            '/target="_parent"\s+href="[^"]*?&turl=(\/[^"]+)"/i',
            'href="$1"',
            $content
        );
        $content = str_replace($unsubUrl, '{UNSUBSCRIBE_URL}', $content);

        // STEP 2: Inject open tracking pixel fresh
        $parts = explode('</body>', $content);
        $img = "<img border='0' src='".$opnUrl."' width='1' height='1' alt='i' id='confirmImg'/>";
        $output = $parts[0].$img.'</body>'.$parts[1];

        // STEP 3: Split head and body
        $head = substr($output, 0, strpos($output, '<body '));
        $body = substr($output, strpos($output, '<body '));

        // STEP 4: Inject click tracking fresh
        $newOutput = $body;
        $newOutput = str_replace('href="http', 'target="_parent" href="'.$clkUrl.'&turl=http', $newOutput);
        $newOutput = str_replace('href="www.', 'target="_parent" href="'.$clkUrl.'&turl=www.', $newOutput);
        $newOutput = str_replace('href="/', 'target="_parent" href="'.$clkUrl.'&turl=/', $newOutput);
        $newOutput = str_replace('{UNSUBSCRIBE_URL}', $unsubUrl, $newOutput);

        $html = $head.$newOutput;

        // STEP 5: Write to file
        $newIndexPath = $dist_path.'/index.html';

        if (! file_exists($newIndexPath)) {
            header('HTTP/1.1 404');
            echo json_encode(['message' => "File not found: {$newIndexPath}"]);

            return;
        }

        file_put_contents($newIndexPath, $html);

        // STEP 6: Copy to blade email template
        $dist_file = resource_path('views/emails/').$templateID.'.blade.php';
        File::copy($newIndexPath, $dist_file);

        header('HTTP/1.1 200');
        echo json_encode(['success' => "Written to file {$newIndexPath}"]);

    }

    public function uploadAsset(Request $request)
    {
        // Get the Template ID posted to the server
        // Template ID and type are configured in your BuilderJS initialization code
        $templateID = $request->template_id;
        $type = $request->type;

        // Get the directory path of the specified template on the hosting server
        // Path may look like this: /storage/templates/{type}/{ID}/

        $path = public_path('templates/').$type.'/'.$templateID.'/';

        if ($_POST['assetType'] == 'upload') {
            // Get uploaded file name
            $filename = $_FILES['file']['name'];

            // Escape sensitive characters in file name
            $filename = preg_replace('/[^a-z0-9\._\-]+/i', '_', $filename);

            // Storage path of the uploaded asset:
            // For example: /storage/templates/{type}/{ID}/Uploaded-Image.PNG
            $filepath = "{$path}/{$filename}";

            // Process uploaded file
            move_uploaded_file($_FILES['file']['tmp_name'], $filepath);
        } elseif ($_POST['assetType'] == 'url') {
            // upload file by upload image
            $filename = uniqid();

            // Storage path of the uploaded asset:
            // For example: /storage/templates/{type}/{ID}/604ce5e36d0fa
            $filepath = "{$path}/{$filename}";

            // Download the file's content
            $content = file_get_contents($_POST['url']);

            // Store it:
            file_put_contents($filepath, $content);
        } elseif ($_POST['assetType'] == 'base64') {
            // upload file by upload image
            $filename = uniqid();

            // Storage path of the uploaded asset:
            // For example: /storage/templates/{type}/{ID}/604ce5e36d0fa
            $filepath = "{$path}/{$filename}";

            // Store it
            file_put_contents($filepath, file_get_contents($_POST['url_base64']));
        }

        // Return the relative URL of the asset
        // Set up HTTP header for response
        header('Content-Type: application/json');
        header('HTTP/1.1 200');
        echo json_encode(['url' => $filename]);

    }

    public function savethumbnail(Request $request)
    {
        $base64 = $request->data;
        $path = public_path('templates/').'user'.'/'.$request->templateId.'/thumb.png';
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        file_put_contents($path, $data);
    }

    public function testEmailSending(Request $request)
    {
        $templateId = $request->templateId;
        $address = $request->address;
        $param = [];

        $result = Mail::send('emails.'.$templateId, $param, function ($message) use ($address) {
            $message->to($address, 'Hybridmail Techics')->subject('Template email sending');
            $message->from('no-reply@hybridmail.techics.co', 'Hybridmail Techics');
        });

        return redirect()->route('app.template.index')->with('success', 'The email is successfully sent.');
    }

    public function serveTemplate(Request $request, $id)
    {
        $path = public_path("templates/user/{$id}/index.html");

        if (! file_exists($path)) {
            abort(404);
        }

        return response(file_get_contents($path), 200)
            ->header('Content-Type', 'text/html')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
