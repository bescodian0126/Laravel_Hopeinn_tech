<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\Mlm;
use App\Models\GatewayCurrency;
use App\Models\Plan;
use App\Models\Node;
use App\Models\User;
use Illuminate\Http\Request;

class NextNode
{
    public $pos_id;
    public $position;
    public $is_blank;

    public function __construct($pos_id, $position, $is_blank)
    {
        $this->pos_id = $pos_id;
        $this->position = $position;
        $this->is_blank = $is_blank;
    }
}

class PlanController extends Controller
{
    function planIndex()
    {
        $user = auth()->user();
        $refuser = User::where('username', $user->ref_user)->first();
        if (!$refuser) {
            print_r("You are not invited by link. You can't use this. Sorry");
            exit;
        }
        $pageTitle = "Plans";
        $plans = Plan::where('status', Status::ENABLE)->orderBy('price', 'asc')->paginate(getPaginate(15));
        $plansCount = count($plans);
        for ($i = 0; $i < $plansCount; $i++) {
            if (Node::where('user_id', $user->id)->where('plan_id', $plans[$i]->id)->exists()) {
                $plans[$i]->active = 1;
            } else {
                $plans[$i]->active = 0;
                if (Node::where('user_id', $refuser->id)->where('plan_id', $plans[$i]->id)->exists())
                    $plans[$i]->refActive = 1;
                else
                    $plans[$i]->refActive = 0;
            }
        }
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();

        // $users = User::where('plan_id', '!=', 0)->get();
        return view($this->activeTemplate . 'user.plan', compact('pageTitle', 'plans', 'gatewayCurrency', 'user'));
    }

    public function getNextNode($currentNode)
    {
        $position = $currentNode->position;
        if ($position == 1 || $position == 2) {
            if (Node::where('pos_id', $currentNode->pos_id)->where('position', $position + 1)->first())
                return new NextNode($currentNode->pos_id, $position + 1, 0);
            else
                return new NextNode($currentNode->pos_id, $position + 1, 1);
        } else {
            $level = 0;
            $parentNode = $currentNode;
            while ($parentNode->position == 3) {
                $parentNode = Node::where('id', $parentNode->pos_id)->first();
                if (!$parentNode) {
                    print_r("Not found parentNode from getNextNode function.");
                    exit;
                }
                $level++;
            }
            $parentNode = Node::where('pos_id', $parentNode->pos_id)->where('position', $parentNode->position + 1)->first();
            $childNode = $parentNode;
            $previouschildNode = $childNode;
            for ($i = 0; $i < $level; $i++) {
                $childNode = Node::where('pos_id', $childNode->id)->where('position', 1)->first();
                if ($i == $level - 2)
                    $previouschildNode = $childNode;
            }
            if (!$childNode)
                return new NextNode($previouschildNode->id, 1, 1);
            else
                return new NextNode($childNode->pos_id, $childNode->position, 0);
            // return $childNode;
        }
    }

    public function searchBlank($refNode)
    {
        $level = 1;

        while (1) {
            $currentNode = $refNode;
            for ($i = 0; $i < $level; $i++) {
                $beforeNode = $currentNode;
                $currentNode = Node::where('pos_id', $currentNode->id)->where('position', 1)->first();
            }

            if (!$currentNode)
                return new NextNode($beforeNode->id, 1, 0);
            //  return $currentNode;
            $nextNode = $currentNode;
            for ($i = 0; $i < 3 ** $level - 1; $i++) {
                $NextNode = $this->getNextNode($nextNode);
                if ($NextNode->is_blank)
                    return $NextNode;
                else
                    $nextNode = Node::where('pos_id', $NextNode->pos_id)->where('position', $NextNode->position)->first();
            }
            $level++;
        }
    }

    public function makeNewNode($plan, $userId, $mode = 0)
    {
        $refuser = User::where('id', $userId)->first()->ref_user;
        $position = User::where('id', $userId)->first()->position;
        // print_r($refuser);exit;

        $refuser_id = User::where('username', $refuser)->first()->id;
        $temp_refuser_id = $refuser_id;

        $temp_refuser = User::where('username', $refuser)->first();
        $refNode = Node::where('user_id', $refuser_id)->where('plan_id', $plan->id)->first();
        $temp_refnode = $refNode;
        while (!$temp_refnode) {
            if($temp_refuser_id == 1) {
                $temp_refnode = Node::where('plan_id', $plan->id)->where('user_id', 1)->first();
                break;
            }
            $temp_refuser = User::where('username', $temp_refuser->ref_user)->first();
            $temp_refuser_id = $temp_refuser->id;
            $temp_refnode = Node::where('user_id', $temp_refuser_id)->where('plan_id', $plan->id)->first();            
        }

        $refNode = $temp_refnode;
        // print_r($refNode); exit;

        $destNode = null;
        //  general purchase case position is maintained.
        if ($mode == 0 || $mode == 1) {
            if (Node::where('pos_id', $refNode->id)->where('position', $position)->exists()) {
                $refNode = Node::where('pos_id', $refNode->id)->where('position', $position)->first();
                $destNode = $this->searchBlank($refNode);
            } else {
                $destNode = NULL;
            }

        }
        //  Reentry case, search from left to right.
        else if ($mode == 2)
            $destNode = $this->searchBlank($refNode);

        $newNode = new Node();
        if ($destNode) {
            $newNode->pos_id = $destNode->pos_id;
            $newNode->position = $destNode->position;
        } else {
            $newNode->pos_id = $refNode->id;
            $newNode->position = $position;
        }
        $newNode->user_id = $userId;
        $newNode->plan_id = $plan->id;
        if ($mode == 2 && $plan->next_plan_id != 0)
            $newNode->is_advanced = 1;


        $newNode->save();
        // $advanced_num++;

        $trx = getTrx();
        $mlm = new Mlm(auth()->user(), $plan, $trx);
        if ($mode == 2) {
            $mlm->reentryPlan($plan, $userId);
        } else
            $mlm->purchasePlan($plan, $userId, $mode);

        // if($plan->size > 3 && $mode == 0) {
        //     $frontNode = Node::where('user_id', $userId)->where('plan_id', Plan::where('size', $plan->size - 1)->first()->id)->first();
        //     if($frontNode) {
        //         $frontNode->is_advanced = 1; 
        //         $frontNode->save();
        //     }
        // }

        if ($mode == 0) {
            $frontPlan = Plan::where('next_plan_id', $plan->id)->first();
            if ($frontPlan) {
                $frontNode = Node::where('user_id', $userId)->where('plan_id', $frontPlan->id)->first();
                if ($frontNode) {
                    $frontNode->is_advanced = 1;
                    $frontNode->save();
                }
            }
        }

        // if($newNode->position == 3){

        $tempNode = $newNode;

        $requireNum = 0;
        for ($i = 1; $i <= $plan->size; $i++) {
            $requireNum += 3 ** $i;
        }
        $cnt = 0;
        // while($tempNode->position == 3){
        while (1) {
            if ($tempNode->pos_id == 0)
                break;
            $parentNode = Node::where('id', $tempNode->pos_id)->first();
            if (!$parentNode) {
                print_r("There is a error in the nodes table of database.");
                exit;
            }
            $tempNode = $parentNode;
            $cnt++;
            if ($cnt == $plan->size) {
                $num = $mlm->getTreeNum($parentNode->user_id, $plan->id, true);
                if ($num == $requireNum) {

                    // $parentNode->update(['user_id' => 2]);
                    Node::where('id', $parentNode->id)->update(['user_id' => 2]);
                    // print_r($parentNode);exit;

                    $this->makeNewNode($plan, $parentNode->user_id, 2);
                    // $mlm->reentryPlan($parentNode->user_id);
                    $cnt = 0;

                    if ($parentNode->is_advanced == 0 && $plan->next_plan_id != 0) {
                        $advancePlan = Plan::where('id', $plan->next_plan_id)->first();

                        $this->makeNewNode($advancePlan, $parentNode->user_id, 1);
                        // $parentNode->is_advanced = 1;
                        // $parentNode->save();                  
                    }
                    // if($parentNode->is_advanced == 0 && $plan->size < 6) {
                    //     $advancePlan = Plan::where('size', $plan->size + 1)->first();
                    //     $this->makeNewNode($advancePlan, $parentNode->user_id, 1);
                    //     // $parentNode->is_advanced = 1;
                    //     // $parentNode->save();                  
                    // }
                    // else break;
                } else
                    break;
            }
        }
        // }
    }

    function planPurchase(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'payment_method' => 'required'
        ]);

        $plan = Plan::where('status', Status::ENABLE)->findOrFail($request->id);
        $user = auth()->user();
        if ($user->balance < $plan->price) {
            $notify[] = ['error', 'You\'ve no sufficient balance'];
            return back()->withNotify($notify);
        }

        if ($request->payment_method != 'balance') {
            $gate = GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', Status::ENABLE);
            })->find($request->payment_method);

            if (!$gate) {
                $notify[] = ['error', 'Invalid gateway'];
                return back()->withNotify($notify);
            }

            if ($gate->min_amount > $plan->price || $gate->max_amount < $plan->price) {
                $notify[] = ['error', 'Plan price crossed gateway limit.'];
                return back()->withNotify($notify);
            }

            $data = PaymentController::insertDeposit($gate, $plan->price, $plan);
            session()->put('Track', $data->trx);
            return to_route('user.deposit.confirm');
        }

        $newNode = $this->makeNewNode($plan, $user->id);

        // add node to tree structure.

        // Add distributions.

        // $reupper = $newNode;
        // $reenter_event_cnt = 0;
        // print_r($plan->size);exit;
        // if($newNode->position == 3){
        //     $tempNode = $newNode;
        //     $cnt = 0;
        //     while($tempNode->position == 3){
        //         $parentNode = Node::where('id', $tempNode->pos_id)->first();
        //         $tempNode = $parentNode;
        //         $cnt++;
        //         if($cnt == $plan->size) {

        //             if($parentNode->reenter_count == 0 && $plan->size < 6) {
        //                 $advancePlanId = Plan::where('size', $plan->size + 1)->first()->id;
        //                 $this->makeNewNode($advancePlanId);
        //             }
        //             $parentNode->reenter_count += 1;
        //             $parentNode->save();
        //             $cnt = 0;
        //         }
        //     }          
        // }

        // $trx = getTrx();

        // $mlm = new Mlm($user, $plan, $trx);
        // $mlm->purchasePlan();

        $notify[] = ['success', ucfirst($plan->name) . ' plan purchased Successfully'];
        return redirect()->back()->withNotify($notify);
    }
}