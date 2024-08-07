<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Models\AdminNotification;
use App\Models\BvLog;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserExtra;
use App\Models\UserLogin;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function dashboard()
    {
        $pageTitle = 'Dashboard';

        // User Info
        $widget['total_users']             = User::count();
        $widget['verified_users']          = User::active()->count();
        $widget['email_unverified_users']  = User::emailUnverified()->count();
        $widget['mobile_unverified_users'] = User::mobileUnverified()->count();

        $widget['total_invest'] = User::sum('total_invest');
        $widget['last7days_invest'] = Transaction::whereDate('created_at', '>=', Carbon::now()->subDays(6))->where('remark', 'purchased_plan')->sum('amount');
        $widget['total_ref_com'] = User::sum('total_ref_com');
        $widget['total_binary_com'] = User::sum('total_binary_com');

        $widget['totalBvCut'] = BvLog::where('trx_type', '-')->sum('amount');
        $widget['bvLeft'] = UserExtra::sum('bv_left');
        $widget['bvRight'] = UserExtra::sum('bv_right');

        // user Browsing, Country, Operating Log
        $userLoginData = UserLogin::where('created_at', '>=', Carbon::now()->subDay(30))->get(['browser', 'os', 'country']);

        $chart['user_browser_counter'] = $userLoginData->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_os_counter'] = $userLoginData->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_country_counter'] = $userLoginData->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);


        $deposit['total_deposit_amount']        = Deposit::successful()->sum('amount');
        $deposit['total_deposit_pending']       = Deposit::pending()->count();
        $deposit['total_deposit_rejected']      = Deposit::rejected()->count();
        $deposit['total_deposit_charge']        = Deposit::successful()->sum('charge');

        $withdrawals['total_withdraw_amount']   = Withdrawal::approved()->sum('amount');
        $withdrawals['total_withdraw_pending']  = Withdrawal::pending()->count();
        $withdrawals['total_withdraw_rejected'] = Withdrawal::rejected()->count();
        $withdrawals['total_withdraw_charge']   = Withdrawal::approved()->sum('charge');


        $trxReport['date'] = collect([]);
        $plusTrx = Transaction::where('trx_type','+')->where('created_at', '>=', Carbon::now()->subDays(30))
                                       ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date")
                                       ->orderBy('created_at')
                                       ->groupBy('date')
                                       ->get();

        $plusTrx->map(function ($trxData) use ($trxReport) {
            $trxReport['date']->push($trxData->date);
        });

        $minusTrx = Transaction::where('trx_type','-')->where('created_at', '>=', Carbon::now()->subDays(30))
                                       ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date")
                                       ->orderBy('created_at')
                                       ->groupBy('date')
                                       ->get();

        $minusTrx->map(function ($trxData) use ($trxReport) {
            $trxReport['date']->push($trxData->date);
        });

        $trxReport['date'] = dateSorting($trxReport['date']->unique()->toArray());


        // Monthly Deposit & Withdraw Report Graph
        $report['months'] = collect([]);
        $report['deposit_month_amount'] = collect([]);
        $report['withdraw_month_amount'] = collect([]);

        $depositsMonth = Deposit::where('created_at', '>=', Carbon::now()->subYear())
            ->where('status', Status::PAYMENT_SUCCESS)
            ->selectRaw("SUM( CASE WHEN status = ".Status::PAYMENT_SUCCESS." THEN amount END) as depositAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')->get();

        $depositsMonth->map(function ($depositData) use ($report) {
            $report['months']->push($depositData->months);
            $report['deposit_month_amount']->push(getAmount($depositData->depositAmount));
        });
        $withdrawalMonth = Withdrawal::where('created_at', '>=', Carbon::now()->subYear())->where('status', Status::PAYMENT_SUCCESS)
            ->selectRaw("SUM( CASE WHEN status = ".Status::PAYMENT_SUCCESS." THEN amount END) as withdrawAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')->get();
        $withdrawalMonth->map(function ($withdrawData) use ($report){
            if (!in_array($withdrawData->months,$report['months']->toArray())) {
                $report['months']->push($withdrawData->months);
            }
            $report['withdraw_month_amount']->push(getAmount($withdrawData->withdrawAmount));
        });

        $months = $report['months'];

        for($i = 0; $i < $months->count(); ++$i) {
            $monthVal      = Carbon::parse($months[$i]);
            if(isset($months[$i+1])){
                $monthValNext = Carbon::parse($months[$i+1]);
                if($monthValNext < $monthVal){
                    $temp = $months[$i];
                    $months[$i]   = Carbon::parse($months[$i+1])->format('F-Y');
                    $months[$i+1] = Carbon::parse($temp)->format('F-Y');
                }else{
                    $months[$i]   = Carbon::parse($months[$i])->format('F-Y');
                }
            }
        }
        
       
        $transactions_not_null = Transaction::whereNotNull('plan_id')->get();

        // foreach ($transactions_not_null as $trans) {
        //     $plan = Plan::find($trans->plan_id);

        //     if ($plan) {
        //         $plan_size = $plan->size;

        //         for ($i = 1; $i <= $plan_size; $i++) {
        //             $next_trans = Transaction::where('id', $trans->id + $i)->first();

        //             if ($next_trans) {
        //                 $next_trans->plan_id = $trans->plan_id;
        //                 $next_trans->save();
        //             } else {
        //                 break; // Stop if there are no more transactions
        //             }
        //         }
        //     }
        // }
        // $transactions = Transaction::all();
        // $plans = Plan::all();
        // foreach($transactions as $trans){
        //     foreach($plans as $plan){
        //         if(strpos($trans->details, "Purchased {$plan->name}") !== false){
        //             $trans->plan_id = $plan->id;
        //             $trans->save();
        //             break;
        //         } 
        //     }
        // }
        
        return view('admin.dashboard', compact('pageTitle', 'widget', 'chart','deposit','withdrawals','depositsMonth','withdrawalMonth','months','trxReport','plusTrx','minusTrx'));
    }


    public function profile()
    {
        $pageTitle = 'Profile';
        $admin = auth('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])]
        ]);
        $user = auth('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image;
                $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return to_route('admin.profile')->withNotify($notify);
    }


    public function password()
    {
        $pageTitle = 'Password Setting';
        $admin = auth('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = auth('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('admin.password')->withNotify($notify);
    }

    public function notifications(){
        $notifications = AdminNotification::orderBy('id','desc')->with('user')->paginate(getPaginate());
        $pageTitle = 'Notifications';
        return view('admin.notifications',compact('pageTitle','notifications'));
    }


    public function notificationRead($id){
        $notification = AdminNotification::findOrFail($id);
        $notification->is_read = Status::YES;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function requestReport()
    {
        $pageTitle = 'Your Listed Report & Request';
        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASECODE');
        $url = "https://license.viserlab.com/issue/get?".http_build_query($arr);
        $response = CurlRequest::curlContent($url);
        $response = json_decode($response);
        if ($response->status == 'error') {
            return to_route('admin.dashboard')->withErrors($response->message);
        }
        $reports = $response->message[0];
        return view('admin.reports',compact('reports','pageTitle'));
    }

    public function reportSubmit(Request $request)
    {
        $request->validate([
            'type'=>'required|in:bug,feature',
            'message'=>'required',
        ]);
        $url = 'https://license.viserlab.com/issue/add';

        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASECODE');
        $arr['req_type'] = $request->type;
        $arr['message'] = $request->message;
        $response = CurlRequest::curlPostContent($url,$arr);
        $response = json_decode($response);
        if ($response->status == 'error') {
            return back()->withErrors($response->message);
        }
        $notify[] = ['success',$response->message];
        return back()->withNotify($notify);
    }

    public function readAll(){
        AdminNotification::where('is_read',Status::NO)->update([
            'is_read'=>Status::YES
        ]);
        $notify[] = ['success','Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')).'- attachments.'.$extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }


}
