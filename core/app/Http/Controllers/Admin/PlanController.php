<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Node;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    
    public function plan()
    {
        $pageTitle = 'Plans';
        $plans = Plan::orderBy('price', 'asc')->paginate(getPaginate());

        // print_r($plans);exit;
        return view('admin.plan.index', compact('pageTitle', 'plans'));
    }

    public function planSave(Request $request)
    {
        $this->validate($request, [
            'name'              => 'required',
            'price'             => 'required|numeric|min:0', 
            'fee'               => 'required|min:0|integer',
            'distribution'           => 'required|numeric|min:0',
            'reenter_fee'       => 'required|numeric',
            'advance_fee'       => 'required|numeric',
            // 'tree_com'          => 'required|numeric|min:0',
        ]);
       
        $plan = new Plan();
        if ($request->id) {
            $plan = Plan::findOrFail($request->id);
        }

        $plan->name             = $request->name;
        $plan->price            = $request->price;
        $plan->fee              = $request->fee;
        $plan->reenter_fee         = $request->reenter_fee;
        $plan->advance_fee         = $request->advance_fee;
        $plan->distribution          = $request->distribution;
        $plan->size             = $request->size;        
        
        $plan->save();

        $planId = $plan->id;

        $node =  new Node();

        $node -> user_id = 1;
        $node -> plan_id = $planId;
        $node -> is_advanced = 1;
        $node -> save();
        

        $notify[] = ['success', 'Plan saved successfully'];
        return back()->withNotify($notify);
    }

    public function status($id){
        return Plan::changeStatus( $id);
    }

}
