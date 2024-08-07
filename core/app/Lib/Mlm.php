<?php

namespace App\Lib;

use App\Models\BvLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Node;
use App\Models\Plan;

class Mlm
{
    /**
     * User who subscribe a plan
     *
     * @var object
     */
    public $user;

    /**
     * Plan which subscribed by the user
     *
     * @var object
     */
    public $plan;

    /**
     * General setting
     *
     * @var object
     */
    public $setting;

    /**
     * Transaction number of whole process
     *
     * @var string
     */
    public $trx;

    public $arrayOfStrings=[];

    /**
     * Initialize some global properties
     *
     * @param object $user
     * @param object $plan
     * @param string $trx
     * @return void
     */
    public function __construct($user = null, $plan = null, $trx = null)
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->trx = $trx;
        $this->setting = gs();
    }

    /**
     * Get the positioner user object
     *
     * @param object $positioner
     * @param int $position
     * @return object;
     */
    public static function getPositioner($positioner, $position)
    {
        $getPositioner = $positioner;
        while (0 == 0) {
            $getUnder = User::where('pos_id', $positioner->id)->where('position', $position)->first(['id', 'pos_id', 'position', 'username']);
            if ($getUnder) {
                $positioner = $getUnder;
                $getPositioner = $getUnder;
            } else {
                break;
            }
        }
        return $getPositioner;
    }

    /**
     * Give BV to upper positioners
     *
     * @return void
     */
    public function updateBv()
    {
        $user = $this->user;
        $bv = $this->plan->bv;
        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();
            if (!$upper) {
                break;
            }
            if ($upper->plan_id == 0) {
                $user = $upper;
                continue;
            }

            $bvlog = new BvLog();
            $bvlog->user_id = $upper->id;
            $bvlog->trx_type = '+';
            $extra = $upper->userExtra;
            if ($user->position == 1) {
                $extra->total_bv_left += $bv;
                $extra->bv_left += $bv;
                $bvlog->position = '1';
            } else {
                $extra->total_bv_right += $bv;
                $extra->bv_right += $bv;
                $bvlog->position = '2';
            }
            $extra->save();
            $bvlog->amount = $bv;
            $bvlog->details = 'BV from ' . auth()->user()->username;
            $bvlog->save();

            $user = $upper;
        }
    }

    /**
     * Give referral commission to immediate referrer
     *
     * @return void
     */
    public function referralCommission()
    {
        $user = $this->user;
        $referrer = $user->referrer;
        $plan = @$referrer->plan;
        if ($plan) {
            $amount = $plan->ref_com;
            $referrer->balance += $amount;
            $referrer->total_ref_com += $amount;
            $referrer->save();

            $trx = $this->trx;
            $transaction = new Transaction();
            $transaction->user_id = $referrer->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $referrer->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Direct referral commission from ' . $user->username;
            $transaction->trx =  $trx;
            $transaction->remark = 'referral_commission';
            $transaction->save();

            notify($referrer, 'REFERRAL_COMMISSION', [
                'amount' => showAmount($amount),
                'username' => $user->username,
                'post_balance' => $referrer->balance,
                'trx' => $trx
            ]);
        }
    }

    /**
     * Give tree commission to upper positioner
     *
     * @return void
     */
    public function treeCommission()
    {
        $user = $this->user;
        $amount = $this->plan->tree_com;
        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();
            if (!$upper) {
                break;
            }
            if ($upper->plan_id == 0) {
                $user = $upper;
                continue;
            }
            $upper->balance += $amount;
            $upper->total_binary_com += $amount;
            $upper->save();

            $trx = $this->trx;
            $transaction = new Transaction();
            $transaction->user_id = $upper->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $upper->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Tree commission';
            $transaction->remark = 'binary_commission';
            $transaction->trx =  $trx;
            $transaction->save();

            notify($upper, 'TREE_COMMISSION', [
                'amount' => showAmount($amount),
                'post_balance' => $upper->balance,
            ]);

            $user = $upper;
        }
    }

    /**
     * Update paid count users to upper positioner when user subscribe a plan
     *
     * @return void
     */
    public function updatePaidCount()
    {
        $user = $this->user;
        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();
            if (!$upper) {
                break;
            }

            $extra = $upper->userExtra;
            if ($user->position == 1) {
                $extra->free_left -= 1;
                $extra->paid_left += 1;
            } else if ($user->position == 3) {
                $extra->free_right -= 1;
                $extra->paid_right += 1;
            } else{
                $extra->free_center -= 1;
                $extra->paid_center += 1;
            }
            $extra->save();
            $user = $upper;
        }
    }

    /**
     * Update free count users to upper positioner when user register to this system
     *
     * @param object $user
     * @return void
     */
    public static function updateFreeCount($user)
    {
        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();
            if (!$upper) {
                break;
            }

            $extra = $upper->userExtra;
            if ($user->position == 1) {
                $extra->free_left += 1;
            } else {
                $extra->free_right += 1;
            }
            $extra->save();

            $user = $upper;
        }
    }

    /**
     * Check the time for giving the matching bonus
     *
     * @return boolean
     */
    public function checkTime()
    {
        $general = $this->setting;
        $times = [
            'H' => 'daily',
            'D' => 'weekly',
            'd' => 'monthly',
        ];
        foreach ($times as $timeKey => $time) {
            if ($general->matching_bonus_time == $time) {
                $day = Date($timeKey);
                if (strtolower($day) != $general->matching_when) {
                    return false;
                }
            }
        }
        if (now()->toDateString() == now()->parse($general->last_paid)->toDateString()) {
            return false;
        }
        return true;
    }

    /**
     * Update the user BV after getting bonus
     *
     * @param object $general
     * @param object $uex
     * @param integer $paidBv
     * @param float $weak
     * @param float $bonus
     * @return void
     */
    public function updateUserBv($uex, $paidBv, $weak, $bonus)
    {
        $general = $this->setting;
        $user = $uex->user;
        //cut paid bv from both
        if ($general->cary_flash == 0) {
            $uex->bv_left -= $paidBv;
            $uex->bv_right -= $paidBv;
            $lostl = 0;
            $lostr = 0;
        }

        //cut only weaker bv from both
        if ($general->cary_flash == 1) {
            $uex->bv_left -= $weak;
            $uex->bv_right -= $weak;
            $lostl = $weak - $paidBv;
            $lostr = $weak - $paidBv;
        }

        //cut all bv from both
        if ($general->cary_flash == 2) {
            $uex->bv_left = 0;
            $uex->bv_right = 0;
            $lostl = $uex->bv_left - $paidBv;
            $lostr = $uex->bv_right - $paidBv;
        }
        $uex->save();
        $bvLog = null;
        if ($paidBv != 0) {
            $bvLog[] = [
                'user_id' => $user->id,
                'position' => 1,
                'amount' => $paidBv,
                'trx_type' => '-',
                'details' => 'Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
            $bvLog[] = [
                'user_id' => $user->id,
                'position' => 2,
                'amount' => $paidBv,
                'trx_type' => '-',
                'details' => 'Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
        }
        if ($lostl != 0) {
            $bvLog[] = [
                'user_id' => $user->id,
                'position' => 1,
                'amount' => $lostl,
                'trx_type' => '-',
                'details' => 'Flush ' . showAmount($lostl) . ' BV after Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
        }
        if ($lostr != 0) {
            $bvLog[] = [
                'user_id' => $user->id,
                'position' => 2,
                'amount' => $lostr,
                'trx_type' => '-',
                'details' => 'Flush ' . showAmount($lostr) . ' BV after Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
        }

        if ($bvLog) {
            BvLog::insert($bvLog);
        }
    }


    /**
     * Get the under position user
     *
     * @param integer $id
     * @param integer $position
     * @return object
     */

    protected function getPositionUser($userId, $position, $planId)
    {
        $nodeTemp = Node::where('user_id', $userId)->where('plan_id', $planId)->first();
        if($nodeTemp) $nodeId = $nodeTemp->id;
        else return false;

        $node = Node::where('pos_id', $nodeId)->where('plan_id', $planId)->where('position', $position)->first();
        if($node) {
            return User::where('id', $node->user_id)->first();
        }
        else return false;
    }

    protected static $tree = [];
    protected static $i = 0;

    public function getTreeItem($userId, $planId, $reset = false, $node = null)
    {
        if ($reset) {
            static::$tree = [];
            static::$i = 0;
        }

        if ($node) {
            static::$tree[static::$i] = $node;
            static::$tree[static::$i]->username = "Upgrade";
            // static::$tree[static::$i]->member = 40;
            static::$tree[static::$i]->member = $this->getTreeNum($userId, $planId, true);
            return;
        }

        $nodeTemp = Node::where('user_id', $userId)->where('plan_id', $planId)->first();

        if ($nodeTemp) {
            static::$tree[static::$i] = $nodeTemp; // Assign $nodeTemp directly
            // Retrieve the username from the User model
            $user = User::find($nodeTemp->user_id);
            if ($user) {
                static::$tree[static::$i]->username = $user->username;
                static::$tree[static::$i]->member = $this->getTreeNum($userId, $planId, true);
                // if ($user->id == 2) {
                //     return;
                // }

            } else {
                static::$tree[static::$i]->username = 'Unknown'; // Or set to a default value if user not found
                static::$tree[static::$i]->member = 'Unknown';
            }
            static::$i++;
            $nodeId = $nodeTemp->id;

        } else
            return 0;

        $temp = Node::where('pos_id', $nodeId)->where('plan_id', $planId)->where('position', 1)->first();
        if ($temp) {
            if ($temp->user_id == 2)
                $this->getTreeItem($temp->user_id, $planId, false, $temp);
            else
                $this->getTreeItem($temp->user_id, $planId);
        }

        $temp = Node::where('pos_id', $nodeId)->where('plan_id', $planId)->where('position', 2)->first();
        if ($temp) {
            if ($temp->user_id == 2)
                $this->getTreeItem($temp->user_id, $planId, false, $temp);
            else
                $this->getTreeItem($temp->user_id, $planId);
        }

        $temp = Node::where('pos_id', $nodeId)->where('plan_id', $planId)->where('position', 3)->first();
        if ($temp) {
            if ($temp->user_id == 2)
                $this->getTreeItem($temp->user_id, $planId, false, $temp);
            else
                $this->getTreeItem($temp->user_id, $planId);
        }
        // if($planId == 21){
        //     print_r($temp);exit;
        // }
        return static::$tree;
    }

    /**
     * Get the under position user
     *
     * @param object $user
     * @return array
     */
    // public function showTreePage($user, $isAdmin = false)
    // {
    //     // $plan = $this->plan;
    //     $data=[];
    //     $plans = Plan::all();
    //     $j = 1;
    //     foreach($plans as $plan) {
    //         if (Node::where('user_id', $user->id)->where('plan_id', $plan->id)->doesntExist()) continue;

    //         $data[$j] = [
    //             'plan_name' => $plan->name,
    //             'plan_distribution' => $plan->distribution,
    //             'plan_fee' => $plan->fee,
    //             'plan_size' => $plan->size,
    //             'plan_price' => $plan->price,
    //         ];

    //         $tree = [];





    //         // $hands = [];

    //         // $hands[0] = $user;
    //         // $this->resetTreeNum();
    //         // $hands[0]->num = $this->getTreeNum($user->id, $plan->id, 0);
    //         // // print_r($hands[1]->num);exit;
    //         // $i = 1;
    //         // while(1){
    //         //     $tempInt = intval(($i + 1) / 3);
    //         //     $tempRemeinder = $i % 3;
    //         //     if($tempRemeinder == 0){
    //         //         $direct = 2;        //center
    //         //     } else if($tempRemeinder == 1){
    //         //         $direct = 3;        //right
    //         //     } else{
    //         //         $direct = 1;        //left
    //         //     }

    //         //     $hands[$i] = $this->getPositionUser($hands[$tempInt]->id, $direct, $plan->id);
    //         //     if($hands[$i] == false) {
    //         //         $data[$j]['tree'] = $hands;
    //         //         // print_r($data[1]);exit;
    //         //         break;
    //         //     }
    //         //     $this->resetTreeNum();
    //             $hands[$i]->num = $this->getTreeNum($hands[$i]->id, $plan->id, 0);
    //         //     $i++;
    //         // }
    //         $j++;
    //     }
    //     // print_r("1111111111111111");
    //     // print_r($data);
    //     // print_r("22222222");exit;
    //     return $data;
    // }



    /**
     * Get single user in tree
     *
     * @param object $user
     * @return string
     */
    public function showSingleUserinTree($user, $isAdmin = false)
    {
        $html = '';
        if ($user) {
            if ($user->plan_id == 0) {
                $userType = "free-user";
                $stShow = "Free";
                $planName = '';
            } else {
                $userType = "paid-user";
                $stShow = "Paid";
                $planName = @$user->plan->name;
            }

            $img = getImage(getFilePath('userProfile') . '/' . $user->image, false, true);
            $refby = @$user->referrer->fullname ?? '';

            if ($isAdmin) {
                $hisTree = route('admin.users.binary.tree', $user->username);
            } else {
                $hisTree = route('user.binary.tree', $user->username);
            }

            $extraData = " data-name=\"$user->fullname\"";
            $extraData .= " data-treeurl=\"$hisTree\"";
            $extraData .= " data-status=\"$stShow\"";
            $extraData .= " data-plan=\"$planName\"";
            $extraData .= " data-image=\"$img\"";
            $extraData .= " data-refby=\"$refby\"";
            $extraData .= " data-lpaid=\"" . @$user->userExtra->paid_left . "\"";
            $extraData .= " data-rpaid=\"" . @$user->userExtra->paid_right . "\"";
            $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
            $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
            $extraData .= " data-lbv=\"" . showAmount(@$user->userExtra->bv_left) . "\"";
            $extraData .= " data-rbv=\"" . showAmount(@$user->userExtra->bv_right) . "\"";

            $html .= "<div class=\"user showDetails\" type=\"button\" $extraData>";
            $html .= "<img src=\"$img\" alt=\"*\"  class=\"$userType\">";
            $html .= "<p class=\"user-name\">$user->username</p>";
        } else {
            $img = getImage('assets/images/nouser.png');

            $html .= "<div class=\"user\" type=\"button\">";
            $html .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
            $html .= "<p class=\"user-name\">No User</p>";
        }

        $html .= " </div>";
        $html .= " <span class=\"line\"></span>";

        return $html;
    }


    /**
     * Get the mlm hands for tree
     *
     * @return array
     */
    public function getHands($num)
    {
        $arrayString = [];
        // return ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $i = 0;
        $j = 0;
        for($i = 1; $i <= $num; $i++){
                $arrayString[] = $i;
        }
        return $arrayString;
        // return ['a', 'al', 'ac', 'ar', 'all', 'alc', 'alr', 'acl', 'acc', 'acr', 'arl', 'arc', 'arr', 'alll', 'allc', 'allr', 'alcl', 'alcc', 'alcr', 'alrl', 'alrc', 'alrr', 'acll', 'aclc', 'aclr', 'accl', 'accc', 'accr', 'acrl', 'acrc', 'acrr', 'arll', 'arlc', 'arlr', 'arcl', 'arcc', 'arcr', 'arrl', 'arrc', 'arrr'];
        // $chars = ['l', 'r', 'c'];
        // for($length = 1; $length <= 6; $length++){
        //     $this->generateCombinations($chars, '', $length);
        // }
        // // print_r($this->$arrayOfStrings);exit;
        // return $this->$arrayOfStrings;

    }



    // tree index generation

    // public function generateCombinations($chars, $prefix, $length) {
    //     if ($length === 0) {
    //         $this->$arrayOfStrings[] = $prefix;
    //         return;
    //     }

    //     foreach ($chars as $char) {
    //         print_r($char);
    //         $this->generateCombinations($chars, $prefix . $char, $length - 1);
    //     }
    // }

    // public function getTreeIndex($hands, $user, $depth){
    //     $chars = ['l', 'r', 'c'];
    //     for ($length = 1; $length <= $depth; $length++) {
    //         indexGeneration($hands, $user, $chars, '', $length);
    //     }
    // }

    // public function indexGeneration($hands, $chars, $prefix, $length) {

    //     if ($length === 0) {
    //         $this->$arrayOfStrings[] = $prefix;
    //         return;
    //     }
    //     $i = 1;
    //     foreach ($chars as $char) {
    //         if($prefix != '' && $hands[$prefix]){
    //             $hands[$prefix.$char] = $this->getPositionUser($hands[$prefix]->id, $i);
    //             $i++;
    //         }
    //         indexGeneration($hands, $chars, $prefix . $char, $length - 1);
    //     }
    // }

    public function sysCommission(){
        $user = $this->user;
        $plan = $this->plan;
        $rootuser = User::where('id', 1)->first();
        $rootuser->balance += $plan->tree_com;
        $rootuser->save();
    }

    /**
     * Check the user is in my tree or not
     *
     * @param object $user
     * @return bool
     */
    protected function checkMyTree($user)
    {
        $topUser = User::where('id', $user->pos_id)->first(['id', 'pos_id']);
        if (!$topUser) {
            abort(401);
        }
        if ($topUser->id == auth()->user()->id) {
            return true;
        }
        $this->checkMyTree($topUser);
    }

    protected static $num;
    protected static $level;

    public function getTreeNum($userId, $planId, $reset = false)
    {
        if ($reset) {
            static::$num = 0;
            static::$level = 0;
        }

        $size = Plan::where('id', $planId)->first()->size;

        $nodeTemp = Node::where('user_id', $userId)->where('plan_id', $planId)->first();
        if ($nodeTemp) {
            $nodeId = $nodeTemp->id;
            static::$level++;
            if (static::$level > $size) {  // stop recursion at level 3
                static::$level--;
                return static::$num;
            }
        } else {
            return static::$num;
        }

        $temp = Node::where('pos_id', $nodeId)->where('plan_id', $planId)->where('position', 1)->first();
        if ($temp) {
            static::$num++;
            $this->getTreeNum($temp->user_id, $planId);
        }
        $temp = Node::where('pos_id', $nodeId)->where('plan_id', $planId)->where('position', 2)->first();
        if ($temp) {
            static::$num++;
            $this->getTreeNum($temp->user_id, $planId);
        }
        $temp = Node::where('pos_id', $nodeId)->where('plan_id', $planId)->where('position', 3)->first();
        if ($temp) {
            static::$num++;
            $this->getTreeNum($temp->user_id, $planId);
        }

        static::$level--; // Decrement the level when returning from the recursion
        return static::$num;
    }

    // public function resetTreeNum()
    // {
    //     self::$initialized = false;
    // }


    ////// plan->is_advanced == 1  deducte reenterfee and advancefee
    //////                   == 0   deducte only reenterfee
    public function distribution($user, $plan, $amount){
        // $user = $this->user;
        // $plan = $this->plan;
        // $size = $plan->size;
        $trx = $this->trx;

        $vacancies = 0;

        $nodeTemp = Node::where('user_id', $user->id)->where('plan_id', $plan->id)->first();
        $cnt = 0;
        for($i = 1; $i <= $plan->size; $i++){
            $upperNode = Node::where('id', $nodeTemp->pos_id)->first();
            if($upperNode == null) break;
            $nodeTemp = $upperNode;
            $cnt++;
            $vacancies += 3 ** $i;
        }
        // $totalAmount = $plan->distribution * $amount;
        $totalAmount = (100 - $plan->fee) * $amount / 100;
        $amount1 = $totalAmount / $cnt;

        $nodeTemp = Node::where('user_id', $user->id)->where('plan_id', $plan->id)->first();

        for($i = 0; $i < $cnt; $i++){
            $upperNode = Node::where('id', $nodeTemp->pos_id)->first();
            $receiver = User::where('id', $upperNode->user_id)->first();
            if($receiver->username == 'rootuser') $deducted_amount = 0;
            else {
                if($upperNode->is_advanced == 1 || $plan->next_plan_id == 0) $deducted_amount = $plan->reenter_fee / $vacancies;
                else $deducted_amount = ($plan->reenter_fee + $plan->advance_fee) / $vacancies;
            }

            $receiver->balance += ($amount1 - $deducted_amount);
            $receiver->deducted_balance += $deducted_amount;
            $receiver->save();

            $transaction = new Transaction();
            $transaction->user_id = $receiver->id;
            $transaction->amount = $amount1 - $deducted_amount;
            $transaction->trx_type = '+';
            $transaction->details = 'Received from ' . $user->username;
            $transaction->remark = 'Received distribution';
            $transaction->trx = $trx;
            $transaction->post_balance = $receiver->balance;
            $transaction->deducted_balance = $receiver->deducted_balance;
            $transaction->sender_id = $user->id;
            $transaction->plan_id = $plan->id;
            $transaction->save();

            $nodeTemp = $upperNode;
        }

        $admin = User::where('username', 'rootuser')->first();
        $fee = $amount * ($plan->fee) / 100;
        $admin->balance += $fee;
        $admin->save();
        $transaction = new Transaction();
        $transaction->user_id = 1;
        $transaction->amount = $fee;
        $transaction->trx_type = '+';
        $transaction->details = 'Received from ' . $user->username;
        $transaction->remark = 'Received fee';
        $transaction->trx = $trx;
        $transaction->post_balance = $admin->balance;
        $transaction->deducted_balance = $admin->deducted_balance;
        $transaction->sender_id = $user->id;
        $transaction->plan_id = $plan->id;
        $transaction->save();
    }

    /**
     * Plan subscribe logic
     *
     * @return void
     */
    public function purchasePlan($plan, $userId, $advance)
    {
        // $user = $this->user;
        // $plan = $this->plan;
        $trx = $this->trx;
        $user = User::where('id', $userId)->first();

        $advance_amount = $plan->advance_fee;
        if($advance) {
                $advance_amount = Plan::where('next_plan_id', $plan->id)->first()->advance_fee;
                $user->deducted_balance -= $advance_amount;
                $user->total_invest += $advance_amount;
        }
        else {
                $user->balance -= $plan->price;
                $user->total_invest += $plan->price;
        }
        $user->save();

        $amount = $advance ? $advance_amount : $plan->price;


        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->trx_type = '-';
        $transaction->details = ($advance ? 'Advanced ': 'Purchased ') . $plan->name;
        $transaction->remark = $advance ? 'Advanced_plan': 'purchased_plan';
        $transaction->trx = $trx;
        $transaction->post_balance = $user->balance;
        $transaction->deducted_balance = $user->deducted_balance;
        $transaction->sender_id = $user->id;
        $transaction->plan_id = $plan->id;
        $transaction->save();

        notify($user, 'PLAN_PURCHASED', [
            'plan_name' => $plan->name,
            'price' => showAmount($amount),
            'trx' => $transaction->trx,
            'post_balance' => showAmount($user->balance)
        ]);

        $this->distribution($user, $plan, $amount);
    }

    public function reentryPlan($plan, $userId)
    {
        // $plan = $this->plan;
        $trx = $this->trx;

        $user = User::where('id', $userId)->first();
        $user->deducted_balance -= $plan->reenter_fee;
        $user->total_invest += $plan->reenter_fee;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->amount = $plan->reenter_fee;
        $transaction->trx_type = '-';
        $transaction->details = 'Reentered ' . $plan->name;
        $transaction->remark = 'reentered_plan';
        $transaction->trx = $trx;
        $transaction->post_balance = $user->balance;
        $transaction->deducted_balance = $user->deducted_balance;
        $transaction->sender_id = $user->id;
        $transaction->plan_id = $plan->id;
        $transaction->save();

        notify($user, 'PLAN_REENTERED', [
            'plan_name' => $plan->name,
            'price' => showAmount($plan->reenter_fee),
            'trx' => $transaction->trx,
            'post_balance' => showAmount($user->balance)
        ]);

        $this->distribution($user, $plan, $plan->reenter_fee);
    }


}
