<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnergySellBuyLogo;

class EnergyHistoryController extends Controller
{
    //
    public function index(){
        $user = auth()->user();
        $pageTitle = "Energy Buy and Sell History";
        $energy_history = EnergySellBuyLogo::where("user_id", $user->id)->orderByDesc('created_at')->get();
        return view($this->activeTemplate . "user.energy_shop.energy_history",compact('pageTitle', "energy_history"));
    }
}
