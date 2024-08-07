<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Pin;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class EpinController extends Controller
{
    public function epin()
    {
        $general = gs();
        if($general->epin != Status::ENABLE){
            abort(404);
        }
        $pageTitle = "Recharge Your Wallet";
        $pins = Pin::where('generate_user_id', auth()->id())->latest()->paginate(getPaginate());
        return view($this->activeTemplate.'user.epin_recharge', compact('pageTitle', 'pins'));
    }

    public function eRecharge(Request $request)
    {
        $general = gs();
        if($general->epin != Status::ENABLE){
            abort(404);
        }

        $this->validate($request, [
            'pin' => 'required|exists:pins,pin'
        ]);
        $user = auth()->user();
        $pin = Pin::where('pin', $request->pin)->where('status', Status::NO)->first();
        if(!$pin)
        {
            $notify[] = ['error', 'Already used this pin.'];
            return back()->withNotify($notify);
        }
        $userPin = Pin::where('pin', $request->pin)->where('status', Status::NO)->where('generate_user_id', $user->id)->first();
        if($userPin)
        {
            $notify[] = ['error', 'You can not e-pin recharge to self account.'];
            return back()->withNotify($notify);
        }
        $pin->status = Status::YES;
        $pin->user_id = $user->id;
        $pin->save();
      
        $user->balance += $pin->amount;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $pin->amount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type = '+';
        $transaction->remark = 'epin';
        $transaction->details = 'E-Pin recharge via ' . $pin->pin;
        $transaction->trx = getTrx();
        $transaction->save();

        $deposit = new Deposit();
        $deposit->user_id = $user->id;
        $deposit->method_code = 0;
        $deposit->method_currency = $general->cur_text;
        $deposit->amount = $pin->amount;
        $deposit->rate = 1;
        $deposit->final_amo = $pin->amount;
        $deposit->btc_amo = 0;
        $deposit->btc_wallet = "";
        $deposit->trx = $transaction->trx;
        $deposit->try = 0;
        $deposit->status = Status::PAYMENT_SUCCESS;
        $deposit->save();

        notify($user, 'PIN_RECHARGE', [
            'trx' => $transaction->trx,
            'pin_number' => $pin->pin,
            'amount' => getAmount($pin->amount),
            'post_balance' => getAmount($user->balance),
        ]);

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'Deposit successful via e-pin';
        $adminNotification->click_url = urlPath('admin.deposit.successful');
        $adminNotification->save();

        $notify[] = ['success', 'Balance has been added to your account'];
        return back()->withNotify($notify);
    }

    public function epinRechargeLog()
    {
        $general = gs();
        if($general->epin != Status::ENABLE){
            abort(404);
        }
        $pageTitle = 'Recharge History'; 
        $transactions = Transaction::where('user_id', auth()->id())->where('remark', 'epin')->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.transactions', compact('pageTitle', 'transactions'));
    }


    public function pinGenerate(Request $request)
    {
        $general = gs();
        if($general->epin != Status::ENABLE){
            abort(404);
        }

        $this->validate($request, [
            'amount' => 'required|numeric|gt:0'
        ]);
        $general = gs();
        $user = auth()->user();
        $charge = 0;
        if($general->epin == 1){
            $charge = (($request->amount / 100) * $general->epin_charge);
        }
        
        if( ($request->amount + $charge) > $user->balance){
            $notify[] = ['error', 'You have insufficient balance'];
            return back()->withNotify($notify);
        }

        $user->balance -= ($request->amount + $charge);
        $user->save();

        $pin = new Pin();
        $pin->generate_user_id = $user->id;
        $pin->amount = $request->amount;
        $pin->pin = rand(10000000,99999999).'-'.rand(10000000,99999999).'-'.rand(10000000,99999999).'-'.rand(10000000,99999999);
        $pin->details = "Created via " .$user->username;
        $pin->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $pin->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = $charge;
        $transaction->trx_type = '-';
        $transaction->details = 'Created E-pin';
        $transaction->remark = 'epin';
        $transaction->trx = getTrx();
        $transaction->save();

        $withdraw = new Withdrawal();
        $withdraw->method_id = 0;
        $withdraw->user_id = $user->id;
        $withdraw->amount = $pin->amount;
        $withdraw->currency = $general->cur_text;
        $withdraw->rate = 1;
        $withdraw->charge = $charge;
        $withdraw->final_amount = $pin->amount - $charge;
        $withdraw->after_charge = $pin->amount - $charge;
        $withdraw->trx = $transaction->trx;
        $withdraw->status = Status::PAYMENT_SUCCESS;
        $withdraw->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'Withdraw successful via e-pin';
        $adminNotification->click_url = urlPath('admin.withdraw.approved');
        $adminNotification->save();
        $notify[] = ['success', 'The pin has been created'];
        return back()->withNotify($notify);
    }


}
