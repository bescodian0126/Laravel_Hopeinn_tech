<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\PackageUser;
use App\Models\PackageTransaction;
use App\Models\Node;
use App\Models\User;
use App\Models\Plan;
use App\Models\PackageInvite;
use App\Constants\Status;
use App\Models\Transaction;
use Carbon\Carbon;

class PackageController extends Controller
{
    // Add some function here
    public function index()
    {
        $user = auth()->user();
        $user_nodes = Node::where('user_id', $user->id)->get();
        $plan_ids = $user_nodes->pluck('plan_id')->toArray();
        $packages = Package::whereIn('plan_id', $plan_ids)
            ->where('status', STATUS::ENABLE)
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($packages as $package) {
            $package_user = PackageUser::where('user_id', $user->id)->where('package_id', $package->id)->first();
            if ($package_user) {
                if ($package_user->status == STATUS::PACKAGE_PURCHASED) {
                    $package->active = 1;
                } else{
                    $package->active = 0;
                }
            } else {
                $package->active = 0;
            }
        }
        $pageTitle = "Investment Plans";
        return view($this->activeTemplate . 'user.package.index', compact('pageTitle', 'packages', 'user'));
    }

    public function history()
    {
        $user = auth()->user();

        $package_transactions = PackageTransaction::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $pageTitle = 'Invest History';
        $emptyMessage = 'History is empty';
        return view($this->activeTemplate . 'user.package.history', compact('pageTitle', 'package_transactions', 'emptyMessage'));
    }

    public function purchase(Request $request)
    {
        // dd($request->all());
        $user = auth()->user();
        $request->validate([
            'package_id' => 'required',
            'balance' => 'required'
        ]);

        $package_id = $request->package_id;
        $user_id = $user->id;

        $package = Package::where('id', $package_id)->first();

        if (!$package) {
            $notify[] = ['error', `Package doesn't exists`];
            return back()->withNotify($notify);
        }

        if ($user->balance < $package->price) {
            $notify[] = ['error', 'Your balance is not enough'];
            return back()->withNotify($notify);
        }

        $user->balance -= $package->price;
        $user->save();

        self::generate_package_users($package, $user);
        self::generate_package_transactions($package, $user, $user, $package->price, STATUS::PACKAGE_PURCHASED);    // 1 means purchase transaction
        self::generate_transactions($package, $user, $user, $package->price, STATUS::PACKAGE_PURCHASED);
        self::generate_bonus_distributions($package, $user);

        $notify[] = ['success', 'Investment Plan purchased successfully'];
        return back()->withNotify($notify);
    }

    private function generate_package_users($package, $user)
    {
        $package_user = new PackageUser();
        $package_user->user_id = $user->id;
        $package_user->package_id = $package->id;
        $package_user->status = STATUS::PACKAGE_PURCHASED;
        $package_user->save();
    }

    private function generate_package_transactions($package, $user, $sender, $amount, $mode)
    {
        $transaction = new PackageTransaction();
        $transaction->user_id = $user->id;
        $transaction->package_id = $package->id;
        $transaction->package_name = $package->name;
        $transaction->mode = $mode;
        $transaction->amount = $amount;
        $transaction->plan_name = $package->plan_name;
        $transaction->plan_id = $package->plan_id;
        $transaction->sender_id = $sender->id;
        if ($mode == STATUS::PACKAGE_PURCHASED)      // purchase transaction
        {
            $transaction->remark = "Purchase Investment Plan";
            $transaction->details = "Purchased " . $package->name;
            $transaction->sender_id = $sender->id;
            $transaction->after_balance = $user->balance;
            $transaction->before_balance = $user->balance + $amount;
            $transaction->trx_type = '-';
        } else if ($mode == STATUS::PACKAGE_NETWORK_BONUS) {    // get Network Bonus
            $transaction->remark = "Get Network Invest bonus";
            $transaction->details = "Get Network bonus from " . $package->name;
            $transaction->after_balance = $user->balance;
            $transaction->before_balance = $user->balance - $amount;
            $transaction->trx_type = '+';
        } else if ($mode == STATUS::PACKAGE_INVITE_BONUS) {       //Get Invite Bonus
            $transaction->remark = "Get Invite bonus";
            $transaction->details = "Get Invite bonus from " . $package->name;
            $transaction->after_balance = $user->balance;
            $transaction->before_balance = $user->balance - $amount;
            $transaction->trx_type = '+';
        } else if ($mode = STATUS::PACKAGE_RELEASED) {
            $transaction->remark = "Get Profit from Invest";
            $transaction->details = "Get Profit from " . $package->name;
            $transaction->after_balance = $user->balance;
            $transaction->before_balance = $user->balance - $amount;
            $transaction->trx_type = '+';
        }
        $transaction->save();
    }

    public function generate_transactions($package, $user, $sender, $amount, $mode)
    {
        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->sender_id = $sender->id;
        $transaction->plan_id = $package->plan_id;
        $transaction->amount = $amount;
        $transaction->charge = $amount;
        $transaction->trx = $trx;
        $transaction->post_balance = $user->balance;
        $transaction->deducted_balance = $user->deducted_balance;
        if ($mode == STATUS::PACKAGE_PURCHASED) {
            $transaction->trx_type = '-';
            $transaction->remark = "Purchase Investment Plan";
            $transaction->details = "Purchased " . $package->name;
        } else if ($mode == STATUS::PACKAGE_NETWORK_BONUS) {
            $transaction->trx_type = '+';
            $transaction->remark = "Get Network Invest bonus";
            $transaction->details = "Get Network bonus from " . $package->name;
        } else if ($mode = STATUS::PACKAGE_INVITE_BONUS) {
            $transaction->trx_type = '+';
            $transaction->remark = "Get Invite bonus";
            $transaction->details = "Get Invite bonus from " . $package->name;
        } else if ($mode = STATUS::PACKAGE_RELEASED) {
            $transaction->trx_type = '+';
            $transaction->remark = "Get Profit from Invest";
            $transaction->details = "Get Profit from " . $package->name;
        }
        $transaction->save();
    }

    private function generate_bonus_distributions($package, $user)
    {
        $percentage = [50, 20, 10, 10, 10];
        $plan = Plan::where('id', $package->plan_id)->first();

        $depth = 5;
        $ref_username = $user->ref_user;
        $ref_user = User::where('username', $ref_username)->first();
        $temp_user = $user;
        $admin = User::where('id', 1)->first();

        if ($ref_user->id != 1) {
            $check_invites = self::check_invites_reached($ref_user, $package);
            if ($check_invites) {
                $ref_user->balance += $package->invite_bonus;
                $ref_user->save();
                self::generate_package_transactions($package, $ref_user, $user, $package->invite_bonus, STATUS::PACKAGE_INVITE_BONUS);
                self::generate_transactions($package, $ref_user, $user, $package->invite_bonus, STATUS::PACKAGE_INVITE_BONUS);
            }
        }

        $index = 0;

        $admin_bonus = $package->price * ($package->returning_price / 100) * ($package->bonus_price / 100);

        for ($i = 0; $i < $depth; $i++) {
            if ($ref_user->id == 1) {
                break;
            }
            $is_user = self::check_user_in_node($plan, $ref_user);
            if ($is_user) {
                $bonus = self::calc_ref_user_bonus($package, $percentage[$index++]);
                $ref_user->balance += $bonus;
                $ref_user->save();
                $admin_bonus -= $bonus;
                self::generate_package_transactions($package, $ref_user, $user, $bonus, STATUS::PACKAGE_NETWORK_BONUS);
                self::generate_transactions($package, $ref_user, $user, $bonus, STATUS::PACKAGE_NETWORK_BONUS);
            }
            $temp_user = $ref_user;
            $ref_user = User::where('username', $temp_user->ref_user)->first();
        }
        if ($admin_bonus != 0) {
            self::generate_package_transactions($package, $admin, $user, $admin_bonus, STATUS::PACKAGE_NETWORK_BONUS);
            self::generate_transactions($package, $admin, $user, $admin_bonus, STATUS::PACKAGE_NETWORK_BONUS);
        }
    }

    public function calc_ref_user_bonus($package, $percentage)
    {
        return $package->price * ($package->returning_price / 100) * ($package->bonus_price / 100) * $percentage / 100;
    }

    private function check_user_in_node($plan, $user)
    {
        $temp_user = Node::where('plan_id', $plan->id)->where('user_id', $user->id)->first();
        if ($temp_user) return true;
        return false;
    }

    private function check_invites_reached($user, $package)
    {
        $package_invite = PackageInvite::where('user_id', $user->id)->where('package_id', $package->id)->first();
        if ($package_invite) {
            if ($package_invite->invite_counts == $package->invite_count - 1) {
                $package_invite->invite_counts = 0;
                $package_invite->save();
                return true;
            }
            $package_invite->invite_counts += 1;
            $package_invite->save();
            return false;
        }
        $package_invite = new PackageInvite();
        $package_invite->user_id = $user->id;
        $package_invite->package_id = $package->id;
        $package_invite->invite_counts = 1;
        $package_invite->save();
        return false;
    }

    private function calc_release_amount($package)
    {
        return $package->price + ($package->price * ($package->returning_price / 100) * (1 - $package->bonus_price / 100));
    }

    public function schedule_func()
    {
        $i = 0;
        $package_users = PackageUser::where('status', '!=', Status::PACKAGE_RELEASED)->get();
        $admin = User::where('id', 1)->first();
        foreach ($package_users as $package_user) {
            $package = Package::where('id', $package_user->package_id)->first();
            $user = User::where('id', $package_user->user_id)->first();
            $duration = $package->repeat_duration;
            $duration_unit = $package->repeat_unit;
            if ($duration_unit == "day") {
                $duration = $duration * 24;
            }
            if ($package_user->updated_at->lt(Carbon::now()->subHours($duration))) {
                $release_amount = self::calc_release_amount($package);
                $user->balance += $release_amount;
                $user->save();
                self::generate_package_transactions($package, $user, $admin, $release_amount, STATUS::PACKAGE_RELEASED);
                self::generate_transactions($package, $user, $admin, $release_amount, STATUS::PACKAGE_RELEASED);
                $package_user->status = STATUS::PACKAGE_RELEASED;
                $package_user->save();
            }
        }
    }
}
