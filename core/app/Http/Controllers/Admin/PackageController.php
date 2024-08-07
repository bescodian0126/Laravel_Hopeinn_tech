<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Plan;

class PackageController extends Controller
{
    // Add functions here
    public function index(){
        $pageTitle = "Investment Plans";
        $packages = Package::orderBy('price', 'asc')->paginate(getPaginate());
        $plans = Plan::orderBy('price', 'asc')->get();
        $emptyMessage = "No Packages";

        return view('admin.package.index', compact('pageTitle', 'packages', 'emptyMessage', 'plans'));
    }

    public function save(Request $request){
        // dd($request->all());
        $this->validate($request, [
            'name'                   => 'required',
            'price'                  => 'required|numeric|min:0', 
            'returning_price'        => 'required|numeric|min:0',
            'bonus_price'            => 'required|numeric|min:0',
            // 'package_type'           => 'required',
            'invite_count'           => 'required|numeric|integer',
            'invite_bonus'           => 'required|numeric|min:0',
            'select_plan'            => 'required|integer',
            'description'            => 'required',
            'duration'               => 'required|integer',
            'select_unit'            => 'required'
        ]);

        $package = new Package();
        if($request->id){
            $package = Package::findOrFail($request->id);
        }

        $package_type = $request->package_type == 'imme_true' ? 1 : 2; // 1 is immediately true, 2 is false

        $plan = Plan::where('id', $request->select_plan)->first();

        $package->name              = $request->name;
        $package->price             = $request->price;
        $package->returning_price   = $request->returning_price;
        $package->bonus_price       = $request->bonus_price;
        $package->type              = $package_type;
        $package->description       = $request->description;
        $package->invite_count      = $request->invite_count;
        $package->invite_bonus      = $request->invite_bonus;
        $package->repeat_duration   = $request->duration;
        $package->repeat_unit       = $request->select_unit;
        $package->plan_id           = $plan->id;
        $package->plan_name         = $plan->name;
        $package->save();

        $notify[] = ['success', 'Package saved successfully'];
        return back()->withNotify($notify);
    }

    public function delete($id){
        $package = Package::where('id', $id)->first();
        $package->delete();
        
        $notify[] = ['success', 'Package deleted successfully'];
        return back()->withNotify($notify);
    }

    public function status($id){
        return Package::changeStatus( $id);
    }

    
}
