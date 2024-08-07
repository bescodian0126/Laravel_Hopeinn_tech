<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Mlm;
use App\Models\BvLog;
use App\Models\Deposit;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Node;
use App\Models\Plan;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ManageUsersController extends Controller
{

    public function allUsers()
    {
        $pageTitle = 'All Users';
        $users = $this->userData();
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function activeUsers()
    {
        $pageTitle = 'Active Users';
        $users = $this->userData('active');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function bannedUsers()
    {
        $pageTitle = 'Banned Users';
        $users = $this->userData('banned');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function emailUnverifiedUsers()
    {
        $pageTitle = 'Email Unverified Users';
        $users = $this->userData('emailUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function kycUnverifiedUsers()
    {
        $pageTitle = 'KYC Unverified Users';
        $users = $this->userData('kycUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function kycPendingUsers()
    {
        $pageTitle = 'KYC Unverified Users';
        $users = $this->userData('kycPending');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function emailVerifiedUsers()
    {
        $pageTitle = 'Email Verified Users';
        $users = $this->userData('emailVerified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    public function mobileUnverifiedUsers()
    {
        $pageTitle = 'Mobile Unverified Users';
        $users = $this->userData('mobileUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    public function mobileVerifiedUsers()
    {
        $pageTitle = 'Mobile Verified Users';
        $users = $this->userData('mobileVerified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function usersWithBalance()
    {
        $pageTitle = 'Users with Balance';
        $users = $this->userData('withBalance');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function paidUsers()
    {
        $pageTitle = 'Paid Users';
        // $users = $this->userData('paid');
        $users = User::has('nodes')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    protected function userData($scope = null)
    {
        $users = User::searchable(['username', 'email']);
        if ($scope) {
            $users = $users->$scope();
        }
        return $users->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function detail($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'User Detail - ' . $user->username;

        $totalDeposit = Deposit::where('user_id', $user->id)->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
        $totalWithdrawals = Withdrawal::where('user_id', $user->id)->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
        $totalTransaction = Transaction::where('user_id', $user->id)->count();
        $totalBv = $totalBv = @$user->userExtra->bv_left + @$user->userExtra->bv_right;
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        // //SSS Get Values which user purchase.
        $nodes = Node::where('user_id', $user->id)->get();
        $plans = $nodes->pluck('plan_id')->unique()->toArray();

        $purchase_plans = Plan::whereIn('id', $plans)->get();

        // $parent_users = [];

        // // Iterate over each plan to find parent user data for its nodes
        // foreach ($purchase_plans as $plan) {
        //     // Find nodes related to the current plan
        //     $plan_nodes = $nodes->where('plan_id', $plan->id);

        //     // Extract parent user data from nodes of the current plan
        //     $plan_parent_user = $plan_nodes->first()->parent ? $plan_nodes->first()->parent->user : null;

        //     // Add parent user data to the array
        //     $parent_users[$plan->id] = $plan_parent_user;
        // }

        // print_r($purchase_plans); exit;

        //SSS Get

        // return view('admin.users.detail', compact('pageTitle', 'user', 'totalDeposit', 'totalWithdrawals', 'totalTransaction', 'totalBv', 'countries', 'purchase_plans', 'parent_users'));
        return view('admin.users.detail', compact('pageTitle', 'user', 'totalDeposit', 'totalWithdrawals', 'totalTransaction', 'totalBv', 'countries', 'purchase_plans'));
    }

    //SSS Search Users
    public function search_users(Request $request){
        if($request->ajax()){
            $query = $request->get('query');
            $users = User::where('username', 'LIKE', "%{$query}%")->get(['id', 'username']);

            return response()->json([
                'type' => 'success',
                'data' => $users
            ]);
        }
    }

    //SSS Check Username
    public function check_username(Request $request){
        if($request->ajax()){
            $username = $request->get('username');
            $user = User::where('username', $username)->first();


            if($user){
                if($user->id == $request->id){
                    return response()->json([
                        'message'=>'current user',
                        'user' => $user
                    ]);
                }
                return response()->json([
                    'message'=>'user found',
                    'user' => $user
                ]);
            } else{
                return response()->json(['message' => 'user not found']);
            }
        }
    }

    //SSS Delete User
    public function delete_user(Request $request, $id){
        $user_id = $id;
        $user = User::where('id', $user_id)->first();
        $user->delete();
        $notify[] = ['success', 'Delete User Successfully'];
        return redirect()->route('admin.dashboard')->withNotify($notify);
    }

    //SSS Delete User from Plan
    public function remove_from_plan(Request $request, $id){
        $user_id = $id;
        $plan_id = $request->remove_plan_id;
        $transactions = Transaction::where('sender_id', $user_id)->where('plan_id', $plan_id)->get();

        $plan = Plan::where('id', $plan_id)->first();

        foreach($transactions as $trans){
            if($trans->user_id == $trans->sender_id) continue;
            $received_user = User::where('id', $trans->user_id)->first();
            $received_user->balance -= $trans->amount;
            $received_user->save();
        }

        $sender = User::where('id', $user_id)->first();
        $sender->balance += $plan->price;
        $sender->save();

        $node = Node::where('user_id', $user_id)->where('plan_id', $plan_id)->first();
        if($node) $node->delete();
        foreach($transactions as $trans){
            $trans->delete();
        }
        $notify[] = ['success', "Successfully removed from {$plan->name}"];
        return back()->withNotify($notify);
    }


    public function kycDetails($id)
    {
        $pageTitle = 'KYC Details';
        $user = User::findOrFail($id);
        return view('admin.users.kyc_detail', compact('pageTitle', 'user'));
    }

    public function kycApprove($id)
    {
        $user = User::findOrFail($id);
        $user->kv = 1;
        $user->save();

        notify($user, 'KYC_APPROVE', []);

        $notify[] = ['success', 'KYC approved successfully'];
        return to_route('admin.users.kyc.pending')->withNotify($notify);
    }

    public function kycReject($id)
    {
        $user = User::findOrFail($id);
        foreach ($user->kyc_data as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $user->kv = 0;
        $user->kyc_data = null;
        $user->save();

        notify($user, 'KYC_REJECT', []);

        $notify[] = ['success', 'KYC rejected successfully'];
        return to_route('admin.users.kyc.pending')->withNotify($notify);
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $username = $request->username;
        if($username != $user->username){
            $new_user = User::where('username', $username)->first();
            if($new_user){
                $notify[] = ['error', 'Please enter new username correctly'];
                return back()->withNotify($notify);
            }
        }

        $password = $request->password;
        if($password){
            $request->validate([
                'password'=>'confirmed',
                'password_confirmation' => 'same:password'
            ]);
        }

        $position = $request->position;

        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryArray = (array) $countryData;
        $countries = implode(',', array_keys($countryArray));

        $countryCode = $request->country;
        $country = $countryData->$countryCode->country;
        $dialCode = $countryData->$countryCode->dial_code;

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|string|max:40|unique:users,email,' . $user->id,
            'mobile' => 'required|string|max:40|unique:users,mobile,' . $user->id,
            'country' => 'required|in:' . $countries,
        ]);
        $user->mobile = $dialCode . $request->mobile;
        $user->country_code = $countryCode;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;

        //SSS change username and password
        $user->username = $request->username;
        $user->password = bcrypt($request->password); // Hashing the password

        //SSS position set
        $user->position = $position;
        $user->ref_user = $request->ref_user;

        $user->address = [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$country,
        ];
        $user->ev = $request->ev ? Status::VERIFIED : Status::UNVERIFIED;
        $user->sv = $request->sv ? Status::VERIFIED : Status::UNVERIFIED;
        $user->ts = $request->ts ? Status::ENABLE : Status::DISABLE;

        if (!$request->kv) {
            $user->kv = 0;
            if ($user->kyc_data) {
                foreach ($user->kyc_data as $kycData) {
                    if ($kycData->type == 'file') {
                        fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
                    }
                }
            }
            $user->kyc_data = null;
        } else {
            $user->kv = 1;
        }
        $user->save();

        $notify[] = ['success', 'User details updated successfully'];
        return back()->withNotify($notify);
    }

    //SSS

    public function addSubBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act' => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $amount = $request->amount;
        $general = gs();
        $trx = getTrx();

        $transaction = new Transaction();

        if ($request->act == 'add') {
            $user->balance += $amount;

            $transaction->trx_type = '+';
            $transaction->remark = 'balance_add';

            $notifyTemplate = 'BAL_ADD';

            $notify[] = ['success', gs('cur_sym') . $amount . ' added successfully'];
        } else {
            if ($amount > $user->balance) {
                $notify[] = ['success', gs('cur_sym') . $amount . ' subtracted successfully'];
                return back()->withNotify($notify);
            }

            $user->balance -= $amount;

            $transaction->trx_type = '-';
            $transaction->remark = 'balance_subtract';

            $notifyTemplate = 'BAL_SUB';
            $notify[] = ['success', $general->cur_sym . $amount . ' subtracted successfully'];
        }

        $user->save();

        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx = $trx;
        $transaction->details = $request->remark;
        $transaction->save();

        notify($user, $notifyTemplate, [
            'trx' => $trx,
            'amount' => showAmount($amount),
            'remark' => $request->remark,
            'post_balance' => showAmount($user->balance)
        ]);

        return back()->withNotify($notify);
    }

    public function login($id)
    {
        Auth::loginUsingId($id);
        return to_route('user.home');
    }

    public function status(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->status == Status::USER_ACTIVE) {
            $request->validate([
                'reason' => 'required|string|max:255'
            ]);
            $user->status = Status::USER_BAN;
            $user->ban_reason = $request->reason;
            $notify[] = ['success', 'User banned successfully'];
        } else {
            $user->status = Status::USER_ACTIVE;
            $user->ban_reason = null;
            $notify[] = ['success', 'User unbanned successfully'];
        }
        $user->save();
        return back()->withNotify($notify);
    }


    public function showNotificationSingleForm($id)
    {
        $user = User::findOrFail($id);
        $general = gs();
        if (!$general->en && !$general->sn) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.users.detail', $user->id)->withNotify($notify);
        }
        $pageTitle = 'Send Notification to ' . $user->username;
        return view('admin.users.notification_single', compact('pageTitle', 'user'));
    }

    public function sendNotificationSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'subject' => 'required|string',
        ]);

        $user = User::findOrFail($id);
        notify($user, 'DEFAULT', [
            'subject' => $request->subject,
            'message' => $request->message,
        ]);
        $notify[] = ['success', 'Notification sent successfully'];
        return back()->withNotify($notify);
    }

    public function showNotificationAllForm()
    {
        $general = gs();
        if (!$general->en && !$general->sn) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }
        $users = User::active()->count();
        $pageTitle = 'Notification to Verified Users';
        return view('admin.users.notification_all', compact('pageTitle', 'users'));
    }

    public function sendNotificationAll(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'subject' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $user = User::active()->skip($request->skip)->first();

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
                'total_sent' => 0,
            ]);
        }

        notify($user, 'DEFAULT', [
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => 'message sent',
            'total_sent' => $request->skip + 1,
        ]);
    }


    public function notificationLog($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'Notifications Sent to ' . $user->username;
        $logs = NotificationLog::where('user_id', $id)->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'user'));
    }

    public function tree(Request $request, $username = null)
    {
        if ($request->search) {
            $user = User::where('username', $request->search)->first();
        } else {
            $user = User::where('username', $username)->first();
        }
        if ($user) {
            $mlm = new Mlm();
            $tree = $mlm->showTreePage($user, true);
            $pageTitle = "Tree of " . $user->fullname;
            return view('admin.users.tree', compact('pageTitle', 'tree', 'mlm'));
        }
        $notify[] = ['error', 'Tree Not Found!!'];
        return to_route('admin.dashboard')->withNotify($notify);
    }
}
