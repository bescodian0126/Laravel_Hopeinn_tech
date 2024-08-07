<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Lib\Mlm;
use App\Models\BvLog;
use App\Models\Deposit;
use App\Models\Form;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Plan;
use App\Models\EnergySellBuyLogo;
use App\Models\GatewayCurrency;
use App\Models\Node;
use App\Models\WithdrawMethod;
use App\Models\UserExtra;
use App\Models\UserRanking;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle = 'Dashboard';
        $user = auth()->user();
        $totalDeposit = Deposit::where('user_id', $user->id)->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
        $submittedDeposit = Deposit::where('status', '!=', Status::PAYMENT_INITIATE)->where('user_id', $user->id)->sum('amount');
        $pendingDeposit = Deposit::pending()->where('user_id', $user->id)->sum('amount');
        $rejectedDeposit = Deposit::rejected()->where('user_id', $user->id)->sum('amount');

        $totalWithdraw = Withdrawal::where('user_id', $user->id)->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
        $submittedWithdraw = Withdrawal::where('status', '!=', Status::PAYMENT_INITIATE)->where('user_id', $user->id)->sum('amount');
        $pendingWithdraw = Withdrawal::pending()->where('user_id', $user->id)->count();
        $rejectWithdraw = Withdrawal::rejected()->where('user_id', $user->id)->sum('amount');

        $totalRef = User::where('ref_by', $user->id)->count();
        $totalBvCut = BvLog::where('user_id', $user->id)->where('trx_type', '-')->sum('amount');
        $totalLeft = @$user->userExtra->free_left + @$user->userExtra->paid_left;
        $totalRight = @$user->userExtra->free_right + @$user->userExtra->paid_right;
        $totalBv = @$user->userExtra->bv_left + @$user->userExtra->bv_right;
        $logs = UserExtra::where('user_id', $user->id)->firstOrFail();
        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'user', 'totalDeposit', 'submittedDeposit', 'pendingDeposit', 'rejectedDeposit', 'totalWithdraw', 'submittedWithdraw', 'pendingWithdraw', 'rejectWithdraw', 'totalRef', 'totalBvCut', 'totalLeft', 'totalRight', 'totalBv', 'logs'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $general = gs();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate . 'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view($this->activeTemplate . 'user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function kycForm()
    {
        if (auth()->user()->kv == 2) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == 1) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'kyc')->first();
        return view($this->activeTemplate . 'user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $user = auth()->user();
        $pageTitle = 'KYC Data';
        return view($this->activeTemplate . 'user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act', 'kyc')->first();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);
        $user = auth()->user();
        $user->kyc_data = $userData;
        $user->kv = 2;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function attachmentDownload($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = auth()->user();
        if ($user->profile_complete == 1) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        if ($user->profile_complete == 1) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'city' => $request->city,
        ];
        $user->profile_complete = Status::YES;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function bvLog(Request $request)
    {
        $user = auth()->user();
        if ($request->type) {
            if ($request->type == 'leftBV') {
                $pageTitle = "Left BV";
                $logs = BvLog::where('user_id', $user->id)->where('position', 1)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            } elseif ($request->type == 'rightBV') {
                $pageTitle = "Right BV";
                $logs = BvLog::where('user_id', $user->id)->where('position', 2)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            } elseif ($request->type == 'cutBV') {
                $pageTitle = "Cut BV";
                $logs = BvLog::where('user_id', $user->id)->where('trx_type', '-')->orderBy('id', 'desc')->paginate(getPaginate());
            } else {
                $pageTitle = "All Paid BV";
                $logs = BvLog::where('user_id', $user->id)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            }
        } else {
            $pageTitle = "BV LOG";
            $logs = BvLog::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(getPaginate());
        }
        return view($this->activeTemplate . 'user.bv_log', compact('pageTitle', 'logs'));
    }

    public function myReferralLog()
    {
        $pageTitle = "My Referral";
        $logs = User::where('ref_user', auth()->user()->username)->orderBy('id', 'desc')->paginate(getPaginate());
        // print_r($logs);exit;
        return view($this->activeTemplate . 'user.my_referral', compact('pageTitle', 'logs'));
    }

    public function binaryTree($user = null)
    {
        $pageTitle = 'Binary Tree';
        // $user = User::where('username', $user)->first();
        $user = auth()->user();
        if (!$user) {
            $user = auth()->user();
        }

        $data = [];
        $plans = Plan::all();

        $j = 1;
        foreach ($plans as $plan) {
            if ($plan->status == 0)
                continue;
            if (Node::where('user_id', $user->id)->where('plan_id', $plan->id)->doesntExist())
                continue;

            $data[$j] = [
                'plan_name' => $plan->name,
                'plan_distribution' => $plan->distribution,
                'plan_fee' => $plan->fee,
                'plan_size' => $plan->size,
                'plan_price' => $plan->price,
            ];

            $tree = [];
            $mlm = new Mlm();
            $tree = $mlm->getTreeItem($user->id, $plan->id, true);

            $data[$j]['tree'] = $tree;
            // print_r($tree);exit;

            $j++;
        }

        return view($this->activeTemplate . 'user.tree', compact('pageTitle', 'data'));
    }

    public function balanceTransfer()
    {
        $general = gs();
        if ($general->balance_transfer != Status::ENABLE) {
            abort(404);
        }
        $pageTitle = 'Transfer Balance';
        return view($this->activeTemplate . 'user.balance_transfer', compact('pageTitle'));
    }

    public function transferConfirm(Request $request)
    {
        $general = gs();
        if ($general->balance_transfer != Status::ENABLE) {
            abort(404);
        }

        $request->validate([
            'username' => 'required|exists:users,username',
            'amount' => 'required|numeric|gt:0'
        ]);

        $user = auth()->user();
        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }

        $toUser = User::where('username', $request->username)->first();

        if ($user->id == $toUser->id) {
            $notify[] = ['error', 'You can\'t send money to your own account'];
            return back()->withNotify($notify);
        }

        $general = gs();
        $amount = $request->amount;
        $fixed = $general->balance_transfer_fixed_charge;
        $percent = $general->balance_transfer_per_charge;
        $charge = ($amount * $percent / 100) + $fixed;
        $withCharge = $amount + $charge;

        if ($user->balance < $withCharge) {
            $notify[] = ['error', 'You have no sufficient balance'];
            return back()->withNotify($notify);
        }

        if ($general->balance_transfer_min > $amount) {
            $notify[] = ['error', 'Please follow minimum balance transfer limit'];
            return back()->withNotify($notify);
        }

        if ($general->balance_transfer_max < $amount) {
            $notify[] = ['error', 'Please follow maximum balance transfer limit'];
            return back()->withNotify($notify);
        }

        $user->balance -= $withCharge;
        $user->save();

        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $withCharge;
        $transaction->post_balance = $user->balance;
        $transaction->charge = $charge;
        $transaction->trx_type = '-';
        $transaction->remark = 'balance_transfer';
        $transaction->details = 'Balance transfer to ' . $toUser->username;
        $transaction->trx = $trx;
        $transaction->save();

        notify($toUser, 'BALANCE_SEND', [
            'amount' => showAmount($amount),
            'charge' => showAmount($charge),
            'username' => $toUser->username,
            'post_balance' => showAmount($user->balance)
        ]);

        $toUser->balance += $amount;
        $toUser->save();

        $transaction = new Transaction();
        $transaction->user_id = $toUser->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $toUser->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->remark = 'balance_transfer';
        $transaction->details = 'Balance receive from ' . $user->username;
        $transaction->trx = $trx;
        $transaction->save();

        notify($toUser, 'BALANCE_RECEIVE', [
            'amount' => showAmount($amount),
            'username' => $user->username,
            'post_balance' => showAmount($toUser->balance)
        ]);

        $notify[] = ['success', 'Balance transferred successfully'];
        return back()->withNotify($notify);
    }

    public function ranking()
    {
        if (!gs()->user_ranking) {
            abort(404);
        }
        $pageTitle = 'User Ranking';
        $userRankings = UserRanking::active()->get();
        $user = auth()->user()->load('userRanking', 'referrals');
        return view($this->activeTemplate . 'user.user_ranking', compact('pageTitle', 'userRankings', 'user'));
    }

    // Energy shop index page
    public function energy_shop_index()
    {
        $user = auth()->user();
        $pageTitle = 'Energy Shop';

        return view($this->activeTemplate . 'user.energy_shop.index', compact('pageTitle', 'user'));
    }

    public function buy_energy()
    {
        $user = auth()->user();
        $balance = $user->balance;

        if ($balance == 0) {
            $notify[] = ['error', 'Your balace is zero now. Please deposit first'];
            return back()->withNotify($notify);
        } else {
            $pageTitle = 'Buy Energy';
            $formatted_balance = ltrim($user->balance, '0'); // Remove leading zeros
            $formatted_balance = number_format((float) $formatted_balance, 2, '.', ''); // Format to two decimal places
            $formatted_energy = ltrim($user->energy, '0'); // Remove leading zeros
            $formatted_energy = number_format((float) $formatted_energy, 2, '.', ''); // Format to two decimal places
            return view($this->activeTemplate . 'user.energy_shop.buy_energy', compact('pageTitle', 'user', 'formatted_balance', 'formatted_energy'));
        }

        // $notify[] = ['success', 'Please get your Energy!'];
        // return back()->withNotify($notify);
    }

    public function sell_energy()
    {
        $user = auth()->user();
        $energy = $user->energy;

        if ($energy == 0) {
            $notify[] = ['error', 'Your balace is zero now. Please deposit first'];
            return back()->withNotify($notify);
        } else {
            $pageTitle = 'Sell Energy';
            $formatted_balance = ltrim($user->balance, '0'); // Remove leading zeros
            $formatted_balance = number_format((float) $formatted_balance, 2, '.', ''); // Format to two decimal places
            $formatted_energy = ltrim($user->energy, '0'); // Remove leading zeros
            $formatted_energy = number_format((float) $formatted_energy, 2, '.', ''); // Format to two decimal places
            return view($this->activeTemplate . 'user.energy_shop.sell_energy', compact('pageTitle', 'formatted_energy', 'formatted_balance'));
        }
    }

    public function charge_energy(Request $request){
        $request->validate([
            'user_balance' => 'required',
            'energy_amount' => 'required|numeric'
        ]);

        $user = auth()->user();
        $amount = $request->energy_amount;
        if($user->balance < $amount){
            $notify[] = ['error','Your Balance is not enough.'];
            return back()->withNotify($notify);
        }
        $user->balance -= $amount;
        $user->energy += $amount;
        $user->save();

        $energy_history = new EnergySellBuyLogo();
        $energy_history->user_id = $user->id;
        $energy_history->username = $user->username;
        $energy_history->amount = $amount;
        $energy_history->type='+';
        $energy_history->current_energy = $user->energy;
        $energy_history->past_energy = $user->energy - $amount;
        $energy_history->remark = 'Energy purchased';
        $energy_history->save();

        $notify[] = ['success','You bought energy successfully'];
        return back()->withNotify($notify);
    }

    public function sell_energy_confirm(Request $request){
        $request->validate([
            'method' => 'required',
            'amount' => 'required|numeric'
        ]);

        $user = auth()->user();
        $amount = $request->amount;
        if($user->energy < $amount){
            $notify[] = ['error','Your Energy is not enough.'];
            return back()->withNotify($notify);
        }

        $user->balance += $amount;
        $user->energy -= $amount;
        $user->save();

        $energy_history = new EnergySellBuyLogo();
        $energy_history->user_id = $user->id;
        $energy_history->username = $user->username;
        $energy_history->amount = $amount;
        $energy_history->type= '-';
        $energy_history->current_energy = $user->energy;
        $energy_history->past_energy = $user->energy + $amount;
        $energy_history->remark = 'Energy Selled';
        $energy_history->save();
        
        $notify[] = ['success','You sold energy successfully'];
        return back()->withNotify($notify);
    }
}
