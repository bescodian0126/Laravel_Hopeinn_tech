<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\Mlm;
use App\Models\AdminNotification;
use App\Models\Frontend;
use App\Models\GatewayCurrency;
use App\Models\Language;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Subscriber;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('ref') && $request->has('position')) {
            return redirect()->route('user.register', $request->only(['ref', 'position']));
        }

        $ref = @$_GET['ref'];
        if ($ref) {
            session()->put('ref', $ref);
        }
        $position = @$_GET['position'];
        if ($position) {
            session()->put('position', $position);
        }
        $pageTitle = 'Home';
        $sections = Page::where('tempname', $this->activeTemplate)->where('slug', '/')->first();
        return view($this->activeTemplate . 'home', compact('pageTitle', 'sections'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname', $this->activeTemplate)->where('slug', $slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        return view($this->activeTemplate . 'pages', compact('pageTitle', 'sections'));
    }


    public function contact()
    {
        $pageTitle = "Contact Us";
        return view($this->activeTemplate . 'contact', compact('pageTitle'));
    }


    public function contactSubmit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();

        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug, $id)
    {
        $policy = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;
        return view($this->activeTemplate . 'policy', compact('policy', 'pageTitle'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return back();
    }

    public function blog()
    {
        $pageTitle = "Blog";
        $blogs = Frontend::where('data_keys', 'blog.element')->latest()->paginate(getPaginate());
        $page = Page::where('tempname', $this->activeTemplate)->where('slug', 'blog')->firstOrFail();
        $sections = $page;
        return view(activeTemplate() . 'blog', compact('pageTitle', 'blogs', 'sections'));
    }

    public function blogDetails($id, $slug)
    {
        $blog = Frontend::where('id', $id)->where('data_keys', 'blog.element')->firstOrFail();
        $latestBlogs = Frontend::where('data_keys', 'blog.element')->where('id', '!=', $blog->id)->orderBy('id', 'desc')->limit(5)->get();
        $pageTitle = 'Blog Details';
        $customPageTitle = $blog->data_values->title;
        $seoContents['keywords']           = $blog->meta_keywords ?? [];
        $seoContents['social_title']       = $blog->data_values->title;
        $seoContents['description']        = strLimit(strip_tags($blog->data_values->description), 150);
        $seoContents['social_description'] = strLimit(strip_tags($blog->data_values->description), 150);
        $seoContents['image']              = getImage('assets/images/frontend/blog/' . @$blog->data_values->blog_image, '770x520');
        $seoContents['image_size']         = '770x520';
        return view($this->activeTemplate . 'blog_details', compact('pageTitle', 'blog', 'latestBlogs', 'seoContents', 'customPageTitle'));
    }


    public function cookieAccept()
    {
        $general = gs();
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy()
    {
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        return view($this->activeTemplate . 'cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null)
    {
        $imgWidth = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font/RobotoMono-Regular.ttf');
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        $general = gs();
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view($this->activeTemplate . 'maintenance', compact('pageTitle', 'maintenance'));
    }
    
    

    public function CheckUsername(Request $request)
    {
        $user = User::where('id', $request->ref_id)->first();
        
        $leftJoinUnderUsername = null;
        $rightJoinUnderUsername = null;
        if ($user == null) {
            $checkUser = false;
        } else {
            $checkUser = true;
            $leftPosition  = Mlm::getPositioner($user, '1');
            $centerPosition = Mlm::getPositioner($user, '2');
            $rightPosition = Mlm::getPositioner($user, '3');

            $leftJoinUnderUsername  = @$leftPosition->username;
            $rightJoinUnderUsername  = @$rightPosition->username;
            $centerJoinUnderUsername = @$centerPosition->username;
            $leftJoinUnderUserId = @$leftPosition->id;
            $centerJoinUnderUserId = @$centerPosition->id;
            $rightJoinUnderUserId = @$rightPosition->id;
        }
        return response()->json([
            'success' => $checkUser,
            'position' => [
                '1' => $leftJoinUnderUsername,
                '2' => $centerJoinUnderUsername,
                '3' => $rightJoinUnderUsername,
                '4'=> $leftJoinUnderUserId,
                '5'=> $centerJoinUnderUserId,
                '6'=> $rightJoinUnderUserId
            ]
        ]);
    }

    public function subscriberStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers',
        ], [
            'email.unique' => 'Already Subscribed'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }
        Subscriber::create([
            'email' => $request->email
        ]);
        return response()->json(['success' => 'Subscribed Successfully']);
    }

    public function plan()
    {
        $pageTitle = "Plan";
        $plans = Plan::where('status', Status::ENABLE)->paginate(getPaginate(12));
        $page = Page::where('tempname', $this->activeTemplate)->where('slug', 'plan')->firstOrFail();
        $sections = $page;
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        return view(activeTemplate() . 'plan', compact('pageTitle', 'plans', 'sections', 'gatewayCurrency'));
    }
}
