<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BvLog;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction(Request $request)
    {
        $pageTitle = 'Transaction Logs';

        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $exactMatch = $request->exact_match ? false : true;
        $transactions = Transaction::searchable(['trx', 'user:username'], $exactMatch)->filter(['trx_type', 'remark'])->dateFilter()->orderBy('id', 'desc')->with('user')->paginate(getPaginate());

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function loginHistory(Request $request)
    {
        $pageTitle = 'User Login History';
        $loginLogs = UserLogin::orderBy('id', 'desc')->searchable(['user:username'])->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip', $ip)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));
    }

    public function notificationHistory(Request $request)
    {
        $pageTitle = 'Notification History';
        $logs = NotificationLog::orderBy('id', 'desc')->searchable(['user:username'])->with('user')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs'));
    }

    public function emailDetails($id)
    {
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle', 'email'));
    }

    public function invest(Request $request)
    {
        $pageTitle = 'Invest Logs';
        $exactMatch = $request->exact_match ? false : true;
        $transactions = Transaction::where('remark', 'purchased_plan')->searchable(['trx', 'user:username'], $exactMatch)->dateFilter()->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.transactions', compact('pageTitle', 'transactions'));
    }

    public function bvLog(Request $request)
    {
        if ($request->type) {
            if ($request->type == 'leftBV') {
                $pageTitle = "Left BV";
                $logs = BvLog::where('position', 1)->where('trx_type', '+')->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
            } elseif ($request->type == 'rightBV') {
                $pageTitle = "Right BV";
                $logs = BvLog::where('position', 2)->where('trx_type', '+')->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
            } elseif ($request->type == 'cutBV') {
                $pageTitle = "Cut BV";
                $logs = BvLog::where('trx_type', '-')->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
            } else {
                $pageTitle = "All Paid BV";
                $logs = BvLog::where('trx_type', '+')->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
            }
        } else {
            $pageTitle = "BV Log";
            $logs = BvLog::searchable(['user:username'], false)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        }
        return view('admin.reports.bvLog', compact('pageTitle', 'logs'));
    }

    public function referralCommission(Request $request)
    {
        $pageTitle = 'Referral Commission Logs';
        $exactMatch = $request->exact_match ? false : true;
        $transactions = Transaction::searchable(['trx', 'user:username'], $exactMatch)->dateFilter()->where('remark', 'referral_commission')->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.reports.transactions', compact('pageTitle', 'transactions'));
    }

    public function binaryCommission(Request $request)
    {
        $pageTitle = 'Binary Commission Logs';
        $exactMatch = $request->exact_match ? false : true;
        $transactions = Transaction::searchable(['trx', 'user:username'], $exactMatch)->dateFilter()->where('remark', 'binary_commission')->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.reports.transactions', compact('pageTitle', 'transactions'));
    }
}
