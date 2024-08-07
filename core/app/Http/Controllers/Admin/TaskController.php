<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Plan;
use App\Models\TaskQuiz;
use App\Models\Answer;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use App\Constants\Status;
use App\Models\TaskTransaction;

class TaskController extends Controller
{
    //
    public function tasks()
    {
        $pageTitle = "Tasks";
        $plans = Plan::all();
        $tasks = Task::with('taskQuizzes.answers')->orderBy("created_at", "desc")->get();
        return view('admin.task.index', compact('pageTitle', 'tasks', 'plans'));
    }



    public function task(Request $request)
    {
        $pageTitle = "Add Questions";
        $data = $request->all();
        $task = Task::with('taskQuizzes.answers')->where('id', $data['id'])->orderBy("created_at", "desc")->get()->first();
        // print_r($task); exit;
        return view('admin.task.add_questions', compact('pageTitle', 'task'));
    }

    public function status($id)
    {
        return Task::changeStatus($id);
    }

    public function task_delete(Request $request, $id)  {
        $task_id = $id;
        

        $task_quizzes = TaskQuiz::where('task_id', $task_id)->get();

        foreach($task_quizzes as $quiz){
            Answer::where('question_id', $quiz->id)->delete();
        }

        TaskQuiz::where('task_id', $task_id)->delete();

        Task::where('id', $task_id)->delete();

        $notify[] = ['success', 'Task deleted successfully'];
        return back()->withNotify($notify);
    }

    public function create_question(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'new_question_area' => 'required'
        ]);

        $task_id = $request->id;
        $question = $request->new_question_area;

        $quiz = new TaskQuiz();
        $quiz->task_id = $task_id;
        $quiz->question = $question;
        $quiz->save();
        $notify[] = ['success', 'Question saved successfully'];
        return back()->withNotify($notify);
    }

    public function edit_question(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'edit_question_area' => 'required'
        ]);

        $question_id = $request->id;
        $question = $request->edit_question_area;

        $quiz = TaskQuiz::where('id', $question_id)->first();
        $quiz->question = $question;
        $quiz->save();
        $notify[] = ['success', 'Question changed successfully'];
        return back()->withNotify($notify);
    }

    public function delete_question(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $question_id = $request->id;

        Answer::where('question_id', $question_id)->delete();

        $quiz = TaskQuiz::where('id', $question_id)->first();
        $quiz->delete();
        $notify[] = ['success', 'Delete question successfully'];
        return back()->withNotify($notify);
    }

    public function taskSave(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'required_energy' => 'required|numeric|min:0',
            'reward_energy' => 'required|min:0|numeric',
            'description' => 'required',
        ]);
        
        $plan = Plan::where('id', $request->select_plan)->first();
        $selected_video_option = $request->radio_option ? $request->radio_option : $request->radio_btn_edit;


        if ($selected_video_option == 'local') {
            $this->validate($request, [
                'taskVideoFile' => 'required|file|mimes:mp4,avi,mkv,mov,wmv,flv,webm,mpeg,mpg|max:100000',    // max 4M
            ]);
            $file = $request->file('taskVideoFile');
            $file_original_name = $file->getClientOriginalName();
            $file_name = time() . '_' . $file_original_name;
            $file_path = $file->storeAs('public/quiz_video', $file_name);
            $task = new Task();

            if ($request->id) {
                $task = Task::findOrFail($request->id);
            }

            $task->task_name = $request->name;
            $task->description = $request->description;
            $task->energy_cost = $request->required_energy;
            $task->reward = $request->reward_energy;
            $task->file_path = $file_path;
            $task->video_path_type = 0;
            $task->file_name = $file->getClientOriginalName();
            $task->server_file_name = $file_name;
            $task->net_video_url = '';
            $task->daily_duration = 0;
            $task->invite_counts = 0;
            $task->invite_duration = 0;
            $task->plan_id = $request->select_plan;
            $task->plan_name = $plan->name;
            $task->bonus_energy = $request->bonus_energy;
            $task->bonus_depth = $request->bonus_depth;
            $task->save();
        } elseif ($selected_video_option == 'net_video_url') {
            $this->validate($request, [
                'net_video_url' => 'required'
            ]);

            $task = new Task();

            if ($request->id) {
                $task = Task::findOrFail($request->id);
            }

            $task->task_name = $request->name;
            $task->description = $request->description;
            $task->energy_cost = $request->required_energy;
            $task->reward = $request->reward_energy;
            $task->net_video_url = $request->net_video_url;
            $task->video_path_type = 1;
            $task->file_path = '';
            $task->file_name = '';
            $task->server_file_name = '';
            $task->daily_duration = 0;
            $task->invite_counts = 0;
            $task->invite_duration = 0;
            $task->plan_id = $request->select_plan;
            $task->plan_name = $plan->name;
            $task->bonus_energy = $request->bonus_energy;
            $task->bonus_depth = $request->bonus_depth;
            $task->save();
        }elseif ($selected_video_option == 'daily_duration') {

            
            $this->validate($request, [
                'daily_duration' => 'required'
            ]);

            $task = new Task();

            if ($request->id) {
                $task = Task::findOrFail($request->id);
            }
            

            $task->task_name = $request->name;
            $task->description = $request->description;
            $task->energy_cost = $request->required_energy;
            $task->reward = $request->reward_energy;
            $task->net_video_url = '';
            $task->video_path_type = 2;
            $task->file_path = '';
            $task->file_name = '';
            $task->server_file_name = '';
            $task->daily_duration = $request->daily_duration;
            $task->invite_counts = 0;
            $task->invite_duration = 0;
            $task->plan_id = $request->select_plan;
            $task->plan_name = $plan->name;
            $task->bonus_energy = $request->bonus_energy;
            $task->bonus_depth = $request->bonus_depth;
            $task->save();
        } elseif ($selected_video_option == 'invite_friends') {
            $this->validate($request, [
                'invite_counts' => 'required',
                'invite_duration' => 'required',
            ]);

            $task = new Task();

            if ($request->id) {
                $task = Task::findOrFail($request->id);
            }

            $task->task_name = $request->name;
            $task->description = $request->description;
            $task->energy_cost = $request->required_energy;
            $task->reward = $request->reward_energy;
            $task->net_video_url = '';
            $task->video_path_type = 3;
            $task->file_path = '';
            $task->file_name = '';
            $task->server_file_name = '';
            $task->daily_duration = 0;
            $task->invite_counts = $request->invite_counts;
            $task->invite_duration = $request->invite_duration;
            $task->plan_id = $request->select_plan;
            $task->plan_name = $plan->name;
            $task->bonus_energy = $request->bonus_energy;
            $task->bonus_depth = $request->bonus_depth;
            $task->save();
        }

        $notify[] = ['success', 'Task saved successfully'];
        return back()->withNotify($notify);
    }

    public function taskUpdate(Request $request, $id)
    {

    }

    public function add_answer(Request $request)
    {
        $validatedData = $request->validate([
            // Define validation rules here if necessary
        ]);

        // Process the request data as needed
        $question_id = $request->question_id;
        $answer = $request->answer;
        $true_false = $request->true_false;

        $new_answer = new Answer();
        $new_answer->question_id = $question_id;
        $new_answer->answer = $answer;
        $new_answer->true_false = $true_false;
        $new_answer->save();

        $new_answer_id = $new_answer->id;

        // Perform operations with $data

        // Return a response (e.g., JSON response)
        return response()->json(['message' => 'Answer saved successfully', 'answer_id' => $new_answer_id]);
    }

    public function edit_answer(Request $request)
    {
        $validatedData = $request->validate([
            // Define validation rules here if necessary
        ]);

        // Process the request data as needed
        $answer_id = $request->answer_id;
        $answer = $request->answer;
        $true_false = $request->true_false;

        $exist_answer = Answer::where('id', $answer_id)->first();

        $exist_answer->answer = $answer;
        $exist_answer->true_false = $true_false;

        $exist_answer->save();

        $new_answer_id = $exist_answer->id;

        // Perform operations with $data

        // Return a response (e.g., JSON response)
        return response()->json(['message' => 'Answer saved successfully', 'answer_id' => $new_answer_id]);
    }

    public function delete_answer(Request $request)
    {
        $validatedData = $request->validate([
            // Define validation rules here if necessary
        ]);

        // Process the request data as needed
        $answer_id = $request->answer_id;

        Answer::where('id', $answer_id)->delete();

        // Perform operations with $data

        // Return a response (e.g., JSON response)
        return response()->json(['message' => 'Answer saved successfully', 'answer_id' => $answer_id]);
    }

    public function pending()
    {
        $pageTitle = 'Pending Tasks';
        $tasks_data = $this->tasks_data('pending');
        $tasks = $tasks_data['data'];
        return view('admin.task.tasks', compact('pageTitle', 'tasks'));
    }
    public function approved()
    {
        $pageTitle = 'Approved Tasks';
        $tasks_data = $this->tasks_data('approved');
        $tasks = $tasks_data['data'];
        return view('admin.task.tasks', compact('pageTitle', 'tasks'));
    }
    public function rejected()
    {
        $pageTitle = 'Rejected Tasks';
        $tasks_data = $this->tasks_data('rejected');
        $tasks = $tasks_data['data'];
        return view('admin.task.tasks', compact('pageTitle', 'tasks'));
    }

    public function log(){
        $pageTitle = 'Tasks log';
        $tasks_data = $this->tasks_data($scope = null);
        $tasks = $tasks_data['data'];

        return view('admin.task.tasks', compact('pageTitle', 'tasks'));
    }

    protected function tasks_data($scope = null){
        if($scope){
            $tasks = TaskStatus::$scope();
        } else{
            $tasks = TaskStatus::with(['task', 'user'])->where('status', '!=', STATUS::TASK_PURCHASED)->where('status', '!=', STATUS::TASK_GET_BONUS);
        }

        return[
            'data' => $tasks->orderBy('id', 'desc')->paginate(getPaginate())
        ];
    }

    public function details($id)
    {
        $task = TaskStatus::where('id',$id)->where('status', '!=', Status::TASK_PURCHASED)->where('status', '!=', Status::TASK_GET_BONUS)->with(['task','user'])->firstOrFail();
        $pageTitle = $task->user->username.' Task Requested ' . showAmount($task->amount) . ' '.gs('cur_text');
        return view('admin.task.detail', compact('pageTitle', 'task'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $task_status = TaskStatus::where('id',$request->id)->where('status',Status::TASK_PENDING)->with(['user', 'task'])->firstOrFail();
        $user = $task_status->user;
        $task = $task_status->task;
        $task_plan = Plan::where('id', $task->plan_id)->first();
        
        $user->balance += $task_status->task->reward;
        $user->save();

        $task_status->status = Status::TASK_SUCCESS;
        $task_status->admin_feedback = $request->details;
        $task_status->remark = 'Win Task';
        $task_status->details = 'Win  ' . $task->task_name;
        $task_status->amount = $task->reward;
        $task_status->before_energy = $user->balance - $task_status->task->reward;
        $task_status->after_energy = $user->balance;
        $task_status->plan_id = $task->plan_id;
        $task_status->plan_name = $task_plan->name;
        $task_status->save();

        notify($task_status->user, 'TASK_APPROVE', [
            'method_name' => $task_status->task->task_name,
            'amount' => showAmount($task_status->task->reward),
            'admin_details' => $request->details
        ]);

        
        

        $task_trans = new TaskTransaction();
        $task_trans->user_id = $user->id;
        $task_trans->task_id = $task->id;
        $task_trans->username = $user->username;
        $task_trans->taskname = $task->task_name;
        $task_trans->task_cost = $task->energy_cost;
        $task_trans->task_reward = $task->reward;
        $task_trans->amount = $task->reward;
        $task_trans->trx_type = '+';
        $task_trans->current_energy = $user->balance;
        $task_trans->past_energy = $user->balance - $task->reward;
        $task_trans->details = "Approved " . $task->task_name;
        $task_trans->remark = "Win Task";
        $task_trans->save();


        $admin = User::where('id', 1)->first();
        $amount = $task->reward;
        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->sender_id = $admin->id;
        $transaction->amount = $amount;
        $transaction->trx_type = '+';
        $transaction->plan_id = $task->plan_id;
        $transaction->details = 'Approved  ' . $task->task_name;
        $transaction->remark = 'Win Task';
        $transaction->trx = $trx;
        $transaction->post_balance = $user->balance;
        $transaction->deducted_balance = $user->deducted_balance;
        $transaction->save();

        $notify[] = ['success', 'Task approved successfully'];
        return to_route('admin.task.pending')->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $task_status = TaskStatus::where('id',$request->id)->where('status',Status::TASK_PENDING)->with(['user', 'task'])->firstOrFail();
        $user = $task_status->user;
        $task = $task_status->task;
        $task_plan = Plan::where('id', $task->plan_id)->first();
        
        $user->energy += $task->energy_cost;
        $user->save();

        $task_status->status = Status::TASK_REJECT;
        $task_status->admin_feedback = $request->details;
        $task_status->remark = 'Reject Task';
        $task_status->details = 'Reject  ' . $task->task_name;
        $task_status->amount = $task->reward;
        $task_status->plan_name = $task_plan->name;
        $task_status->before_energy = $user->energy - $task->energy_cost;
        $task_status->after_energy = $user->energy;
        $task_status->plan_id = $task->plan_id;        
        $task_status->save();

        notify($task_status->user, 'TASK_REJECT', [
            'method_name' => $task_status->task->task_name,
            'amount' => showAmount($task_status->task->reward),
            'admin_details' => $request->details
        ]);

        

        $amount = 0;
        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->sender_id = $user->id;
        $transaction->amount = $amount;
        $transaction->trx_type = '-';
        $transaction->plan_id = $task->plan_id;
        $transaction->details = 'Reject  ' . $task->task_name;
        $transaction->remark = 'Reject Task';
        $transaction->trx = $trx;
        $transaction->post_balance = $user->balance;
        $transaction->deducted_balance = $user->deducted_balance;
        $transaction->save();

        // $task_trans = new TaskTransaction();
        // $task_trans->user_id = $user->id;
        // $task_trans->task_id = $task->id;
        // $task_trans->username = $user->username;
        // $task_trans->taskname = $task->task_name;
        // $task_trans->task_cost = $task->energy_cost;
        // $task_trans->task_reward = $task->reward;
        // $task_trans->amount = $task->reward;
        // $task_trans->trx_type = '+';
        // $task_trans->current_energy = $user->energy;
        // $task_trans->past_energy = $user->energy;
        // $task_trans->details = "Reject " . $task->task_name;
        // $task_trans->remark = "Reject Task";
        // $task_trans->save();

        $notify[] = ['success', 'Task rejected successfully'];
        return to_route('admin.task.pending')->withNotify($notify);
    }
    
}
