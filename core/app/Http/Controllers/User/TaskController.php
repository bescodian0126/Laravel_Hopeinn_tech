<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskQuiz;
use App\Models\EnergySellBuyLogo;
use App\Models\User;
use App\Models\Node;
use App\Models\Plan;
use App\Models\TaskTransaction;
use App\Models\TaskStatus;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Constants\Status;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\Continue_;

class TaskController extends Controller
{
    //
    public function index()
    {
        $pageTitle = "Tasks";
        $user = auth()->user();

        $user_nodes = Node::where('user_id', $user->id)->get();
        $plan_ids = $user_nodes->pluck('plan_id')->toArray();

        $tasks = Task::with('taskQuizzes.answers')
            ->whereIn('plan_id', $plan_ids)
            ->where('status', STATUS::ENABLE)
            ->orderBy("created_at", "desc")
            ->get();

        $gateway_currency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        return view($this->activeTemplate . 'user.task.index', compact('pageTitle', 'tasks', 'gateway_currency', 'user'));
    }
    public function start(Request $request)
    {
        $request->validate([
            'task_id' => 'required',
            'payment_method' => 'required'
        ]);


        $task_id = $request->task_id;
        $pageTitle = 'Task Start';
        

        $task = Task::where('status', Status::ENABLE)->findOrFail($task_id);
        $user = auth()->user();
        if ($user->balance < $task->energy_cost) {
            $notify[] = ['error', 'You\'ve no sufficient balance'];
            return back()->withNotify($notify);
        }

        $task_success_fail = TaskStatus::where('user_id', $user->id)->where('task_id', $task->id)->get();

        // foreach($task_success_fail as $check_task){
        //     if($check_task->status == STATUS::TASK_PURCHASED){
        //     $data = [
        //         'pageTitle' => $pageTitle,
        //         'task_status_id' => $check_task->id,
        //         'task_id' => $task_id,
        //         'user_id' => $user->id,
        //         'status' => $check_task->success_fail
        //     ];
    
        //     session(['taskData' => $data]);
        //     return redirect()->route('user.task.quiz')->with('data', $data);
        //     }
        // }
        

        // if ($request->payment_method != 'balance') {
        //     $gate = GatewayCurrency::whereHas('method', function ($gate) {
        //         $gate->where('status', Status::ENABLE);
        //     })->find($request->payment_method);

        //     if (!$gate) {
        //         $notify[] = ['error', 'Invalid gateway'];
        //         return back()->withNotify($notify);
        //     }

        //     if ($gate->min_amount > $task->energy_cost || $gate->max_amount < $task->energy_cost) {
        //         $notify[] = ['error', 'Plan price crossed gateway limit.'];
        //         return back()->withNotify($notify);
        //     }

        //     $data = PaymentController::insertDeposit($gate, $task->energy_cost, $task);
        //     session()->put('Track', $data->trx);
        //     return to_route('user.deposit.confirm');
        // }

        $task_plan = Plan::where('id', $task->plan_id)->first();
        
        
        $amount = $task->energy_cost;
        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->sender_id = $user->id;
        $transaction->plan_id = $task_plan->id;
        
        $transaction->amount = $amount;
        $transaction->trx_type = '-';
        $transaction->details = 'Purchased ' . $task->name;
        $transaction->remark = 'Started Task';
        $transaction->trx = $trx;
        $transaction->post_balance = $user->balance;
        $transaction->deducted_balance = $user->deducted_balance;
        $transaction->save();
        
        

        

        $user_id = $user->id;

        $task_status = new TaskStatus();
        $task_status->user_id = $user_id;
        $task_status->task_id = $task_id;
        $task_status->success_fail = 0;
        $task_status->status = STATUS::TASK_PURCHASED;
        $task_status->remark = 'Started Task';
        $task_status->details = 'Purchased ' . $task->task_name;
        $task_status->amount = $task->energy_cost;
        $task_status->plan_id = $task->plan_id;
        $task_status->sender_id = $user_id;
        $task_status->before_energy = $user->balance;
        $task_status->after_energy = $user->balance - $task->energy_cost;
        $task_status->save();



        self::calc_bonus_distribution($user, $task_plan, $task);
        $user->balance -= $task->energy_cost;
        $user->save();


        // $task_trans = new TaskTransaction();
        // $task_trans->user_id = $user->id;
        // $task_trans->task_id = $task->id;
        // $task_trans->username = $user->username;
        // $task_trans->taskname = $task->task_name;
        // $task_trans->task_cost = $task->energy_cost;
        // $task_trans->task_reward = $task->reward;
        // $task_trans->amount = $task->energy_cost;
        // $task_trans->trx_type = '-';
        // $task_trans->current_energy = $user->energy;
        // $task_trans->status = STATUS::TASK_PURCHASED;
        // $task_trans->past_energy = $user->balance + $task->energy_cost;
        // $task_trans->details = "Purchased " . $task->task_name;
        // $task_trans->remark = "Start Task";
        // $task_trans->save();
        // if ($task_status == null) {
        //     $new_task_status = new TaskStatus();
        //     $new_task_status->user_id = $user_id;
        //     $new_task_status->task_id = $task_id;
        //     $new_task_status->save();
        // } else{
        //     $status = $task_status->status;
        // }

        $data = [
            'pageTitle' => $pageTitle,
            'task_status_id' => $task_status->id,
            'task_id' => $task_id,
            'user_id' => $user_id,
            'status' => $task_status->success_fail
        ];

        session(['taskData' => $data]);



        return redirect()->route('user.task.quiz')->with('data', $data);
        // return view($this->activeTemplate . 'user.task.quiz', compact('pageTitle', 'task_id', 'user_id', 'status'));
    }

    public function handle_task()
    {
        $data = session('taskData', []);
        if (empty($data)) {
            // Handle the case where data is missing (e.g., redirect to an error page or show a message)
            return redirect()->route('user.dashboard')->with('message', 'Data not found.');
        }
        $pageTitle = $data['pageTitle'];
        $task_id = $data['task_id'];
        $user_id = $data['user_id'];
        $status = $data['status'];

        $task_status_id = $data['task_status_id'];

        return view($this->activeTemplate . 'user.task.quiz', compact('pageTitle', 'task_id', 'user_id', 'status', 'task_status_id'));
    }

    public function download_video(Request $request)
    {

        $task_id = $request->task_id;
        $user_id = $request->user_id;

        // Fetch task information
        $task = Task::findOrFail($task_id);

        $task_status_id = $request->task_status_id;

        $questions = TaskQuiz::with('answers')->where('task_id', $task_id)->get();

        $task_status = TaskStatus::where('id', $task_status_id)->first()->success_fail;



        if ($task->video_path_type == 0) {
            $filePath = $task->file_path;

            $fileName = $task->server_file_name;

            // Get full path to the file
            $storageFilePath = storage_path('app/' . $filePath);

            if (!file_exists($storageFilePath)) {
                return response()->json(['error' => 'File not found.'], 404);
            }

            // Read file contents and encode in base64
            $fileContent = base64_encode(file_get_contents($storageFilePath));
            $fileType = mime_content_type($storageFilePath);

            // Return JSON response with file data
            return response()->json([
                'message' => 'success',
                'file_content' => $fileContent,
                'file_name' => $fileName,
                'file_type' => $fileType,
                'task_status' => $task_status,
                // 'task_status' => $task_status->status,
                'questions' => $questions
            ]);
        } else if ($task->video_path_type == 1) {
            $net_video_url = $task->net_video_url;
            return response()->json([
                'message' => 'success',
                'file_content' => 'from tiktok',
                'file_name' => $net_video_url,
                'file_type' => 'tiktok_video',
                'task_status' => $task_status,
                'questions' => $questions
            ]);
        }
    }

    public function check_result(Request $request)
    {
        $result = $request->result;

        $user = User::where('id', $request->user_id)->first();
        $admin = User::where('id', 1)->first();
        $task = Task::where('id', $request->task_id)->first();
        $task_status_id = $request->task_status_id;

        $task_status = TaskStatus::where('id', $task_status_id)->where('user_id', $user->id)->where('task_id', $task->id)->first();

        if ($result == 1) {
            // $user->energy += $task->reward;
            // $admin->energy -= $task->reward;
            // $user->save();
            // $admin->save();
            $task_status->status = STATUS::TASK_PENDING;
            $task_status->success_fail = STATUS::TASK_SUCCESS;
            $task_status->remark = 'Win Task';
            $task_status->details = 'Win  ' . $task->task_name;
            $task_status->amount = $task->reward;
            $task_status->plan_id = $task->plan_id;
            $task_status->before_energy = $user->balance;
            $task_status->after_energy = $user->balance;
            $task_status->save();


            // $task_trans_history = new TaskTransaction();
            // $task_trans_history->user_id = $user->id;
            // $task_trans_history->task_id = $task->id;
            // $task_trans_history->username = $user->username;
            // $task_trans_history->taskname = $task->task_name;
            // $task_trans_history->amount = $task->reward;
            // $task_trans_history->trx_type = '+';
            // $task_trans_history->remark = 'Win Quiz';
            // $task_trans_history->status = STATUS::TASK_PENDING;
            // $task_trans_history->current_energy = $user->energy;
            // $task_trans_history->past_energy = $user->energy - $task->reward;
            // $task_trans_history->save();

            return response()->json([
                'message' => 'success',
                'user_id' => $request->user_id,
                'task_id' => $request->task_id,
                'redirect_url' => route('user.task.index')
            ]);
        } else {
            $task_status->status = STATUS::TASK_FAILED;
            $task_status->success_fail = STATUS::TASK_FAILED;
            $task_status->save();
            return response()->json([
                'message' => 'failed',
                'user_id' => $request->user_id,
                'task_id' => $request->task_id,
                'redirect_url' => route('user.task.index')
            ]);
        }
    }

    public function task_trans_history(Request $request)
    {
        $user = auth()->user();

        $task_transactions = TaskStatus::where('user_id', $user->id)->with(['user', 'task'])->orderBy('created_at', 'desc')->get();
        $pageTitle = 'Task Transactions';

        return view($this->activeTemplate . 'user.task.history', compact('pageTitle', 'task_transactions'));
    }

    public function calc_bonus_distribution($user, $plan, $task)
    {
        $depth = $task->bonus_depth;
        $ref_username = $user->ref_user;
        $ref_user = User::where('username', $ref_username)->first();
        $admin = User::where('id', 1)->first();



        $admin_bonus = $task->energy_cost - $task->bonus_energy * $task->bonus_depth;



        for ($i = 0; $i < $depth; $i++) {
            if ($ref_user->id == 1) {
                break;
            }

            $is_user = self::check_user_in_node($plan, $ref_user);

            if ($is_user) {
                $task_trans = new TaskStatus();
                $task_trans->user_id = $ref_user->id;
                $task_trans->task_id = $task->id;
                $task_trans->success_fail = 0;
                $task_trans->status = STATUS::TASK_GET_BONUS;
                $task_trans->remark = 'Received Bonus Fee';
                $task_trans->details = 'Received from  ' . $user->username;
                $task_trans->amount = $task->bonus_energy;
                $task_trans->plan_id = $task->plan_id;
                $task_trans->sender_id = $user->id;
                $task_trans->before_energy = $ref_user->balance;
                $task_trans->after_energy = $ref_user->balance + $task->bonus_energy;
                $task_trans->save();

                $amount = $task->bonus_energy;
                $trx = getTrx();
                $transaction = new Transaction();
                $transaction->user_id = $ref_user->id;
                $transaction->sender_id = $user->id;
                $transaction->amount = $amount;
                $transaction->trx_type = '+';
                $transaction->plan_id = $task->plan_id;
                $transaction->details = 'Received from  ' . $user->username;
                $transaction->remark = 'Received Bonus Fee';
                $transaction->trx = $trx;
                $transaction->post_balance = $ref_user->balance;
                $transaction->deducted_balance = $ref_user->deducted_balance;
                $transaction->save();

                $ref_user->balance += $task->bonus_energy;
                $ref_user->save();
            } else {
                $admin_bonus += $task->bonus_energy;
            }
            $ref_user = User::where('username', $ref_user->ref_user)->first();
        }



        $task_trans = new TaskStatus();
        $task_trans->user_id = $admin->id;
        $task_trans->task_id = $task->id;
        $task_trans->success_fail = 0;
        $task_trans->status = STATUS::TASK_GET_BONUS;
        $task_trans->remark = 'Received Bonus Fee';
        $task_trans->details = 'Received from  ' . $user->username;
        $task_trans->amount = $admin_bonus;
        $task_trans->plan_id = $task->plan_id;
        $task_trans->sender_id = $user->id;
        $task_trans->before_energy = $admin->balance;
        $task_trans->after_energy = $admin->balance - $admin_bonus;
        $task_trans->save();

        $amount = $admin_bonus;
        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $admin->id;
        $transaction->sender_id = $admin->id;
        $transaction->amount = $amount;
        $transaction->trx_type = '+';
        $transaction->plan_id = $task->plan_id;
        $transaction->details = 'Received from  ' . $user->username;
        $transaction->remark = 'Received Bonus Fee';
        $transaction->trx = $trx;
        $transaction->post_balance = $admin->balance;
        $transaction->deducted_balance = $admin->deducted_balance;
        $transaction->save();

        $admin->balance += $admin_bonus;
        $admin->save();
    }

    public function check_user_in_node($plan, $user)
    {
        $temp_user = Node::where('plan_id', $plan->id)->where('user_id', $user->id)->first();

        if ($temp_user) return true;
        return false;
    }
}
