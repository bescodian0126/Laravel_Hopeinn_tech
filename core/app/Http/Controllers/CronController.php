<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Lib\Mlm;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserRanking;
use Carbon\Carbon;

class CronController extends Controller
{
    public function matchingBonus()
    {

        try {
            $mlm = new Mlm();
            $general = gs();
            $general->last_cron = now()->toDateTimeString();
            $general->save();

            $check = $mlm->checkTime();
            if (!$check) {
                return 0;
            }

            $general->last_paid = now()->toDateString();
            $general->save();

            $eligibleUsers = UserExtra::where('bv_left', '>=', $general->total_bv)->where('bv_right', '>=', $general->total_bv)->cursor();

            foreach ($eligibleUsers as $uex) {
                $user = $uex->user;

                //get BV and bonus
                $weak = $uex->bv_left < $uex->bv_right ? $uex->bv_left : $uex->bv_right;
                $weaker = $weak < $general->max_bv ? $weak : $general->max_bv;
                $pair = intval($weaker / $general->total_bv);
                $bonus = $pair * $general->bv_price;
                if ($bonus <= 0) {
                    continue;
                }
                $paidBv = $pair * $general->total_bv;

                $user->balance += $bonus;
                $user->save();

                //create transaction
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = $bonus;
                $transaction->post_balance = $user->balance;
                $transaction->charge = 0;
                $transaction->trx_type = '+';
                $transaction->remark = 'paid_bv';
                $transaction->details = 'Paid ' . $bonus . ' ' . $general->cur_text . ' For ' . $paidBv . ' BV.';
                $transaction->trx =  getTrx();
                $transaction->save();

                $mlm->updateUserBv($uex, $paidBv, $weak, $bonus);

                notify($user, 'MATCHING_BONUS', [
                    'amount' => showAmount($bonus),
                    'paid_bv' => $paidBv,
                    'post_balance' => showAmount($user->balance)
                ]);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }


    public function cronRank()
    {

        try {

            $general = gs();
            if (!$general->user_ranking) {
                return 'MODULE DISABLED';
            }

            $general = gs();
            $general->last_cron = now()->toDateTimeString();
            $general->save();

            $users = User::with('referrals')->orderBy('last_rank_update', 'asc')->limit(100)->get();

            foreach ($users as $user) {

                $user->last_rank_update = now();
                $user->save();

                $userBvLeft = $user->userExtra->total_bv_left;
                $userBvRight = $user->userExtra->total_bv_right;

                $rankings = UserRanking::active()->where('id', '>', $user->user_ranking_id)->where('bv_left', '<=', $userBvLeft)->where('bv_right', '<=', $userBvRight)->get();

                foreach ($rankings as $ranking) {

                    $user->balance += $ranking->bonus;
                    $user->user_ranking_id = $ranking->id;
                    $user->save();

                    $transaction               = new Transaction();
                    $transaction->user_id      = $user->id;
                    $transaction->amount       = $ranking->bonus;
                    $transaction->charge       = 0;
                    $transaction->post_balance = $user->balance;
                    $transaction->trx_type     = '+';
                    $transaction->trx          = getTrx();
                    $transaction->remark       = 'ranking_bonus';
                    $transaction->details      = showAmount($ranking->bonus) . ' ' . $general->cur_text . ' ranking bonus for ' . @$ranking->name;
                    $transaction->save();
                }
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    public function cron()
    {
        $crons = CronJob::query();
        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::ENABLE);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds($cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime = Carbon::parse($cronLog->start_at);
            $endTime = Carbon::parse($cronLog->end_at);
            $diffInSeconds = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->alias) {
            $notify[] = ['success', request()->alias . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }
}
